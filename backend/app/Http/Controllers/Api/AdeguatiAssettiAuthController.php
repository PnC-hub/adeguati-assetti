<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdeguatiAssettiAuthController extends Controller
{
    /**
     * Register new user
     * Supports both imprenditore (default) and consulente registration
     */
    public function register(Request $request): JsonResponse
    {
        $tipoUtente = $request->input('tipo_utente', 'imprenditore');

        // Validation rules depend on user type
        $rules = [
            'nome' => 'required|string|max:100',
            'cognome' => 'required|string|max:100',
            'email' => 'required|email|unique:aa_users,email',
            'password' => 'required|string|min:8|confirmed',
            'tipo_utente' => 'nullable|in:imprenditore,consulente',
            'referral_code' => 'nullable|string|max:20',
            'invite_token' => 'nullable|string|max:64',
        ];

        // Imprenditore needs azienda, Consulente needs studio
        if ($tipoUtente === 'imprenditore') {
            $rules['azienda'] = 'required|string|max:255';
        } else {
            $rules['studio_nome'] = 'required|string|max:255';
            $rules['studio_p_iva'] = 'nullable|string|max:20';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Generate unique referral code for this user
            $myReferralCode = strtoupper(Str::random(8));

            // Check if referred by someone
            $referredBy = null;
            if ($request->referral_code) {
                $referrer = DB::table('aa_users')
                    ->where('referral_code', strtoupper($request->referral_code))
                    ->first();
                if ($referrer) {
                    $referredBy = $referrer->id;
                }
            }

            // Determine starting plan based on user type
            // Imprenditore: free (upgrade to impresa49 via Stripe)
            // Consulente (commercialista): commercialista_free (always free, earns 20% revenue share)
            $piano = $tipoUtente === 'consulente' ? 'commercialista_free' : 'free';
            $trialEndsAt = null; // No trial needed anymore

            // Check if registering via invite token
            $inviteRecord = null;
            if ($request->invite_token) {
                $inviteRecord = DB::table('aa_inviti_bidirezionali')
                    ->where('token', $request->invite_token)
                    ->where('stato', 'pending')
                    ->where('scade_at', '>', now())
                    ->first();
            }

            // Create user
            $userId = DB::table('aa_users')->insertGetId([
                'nome' => $request->nome,
                'cognome' => $request->cognome,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipo_utente' => $tipoUtente,
                'piano' => $piano,
                'trial_ends_at' => $trialEndsAt,
                'referral_code' => $myReferralCode,
                'referred_by' => $referredBy,
                'storico_mesi_extra' => 0,
                'referral_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $studioId = null;
            $aziendaId = null;

            if ($tipoUtente === 'consulente') {
                // Create studio for consulente
                $studioId = DB::table('aa_studi')->insertGetId([
                    'user_id' => $userId,
                    'nome' => $request->studio_nome,
                    'p_iva' => $request->studio_p_iva,
                    'email' => $request->email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update user with studio_id
                DB::table('aa_users')->where('id', $userId)->update([
                    'studio_id' => $studioId,
                ]);
            } else {
                // Create default company for imprenditore
                $aziendaId = DB::table('aa_aziende')->insertGetId([
                    'user_id' => $userId,
                    'nome' => $request->azienda,
                    'dimensione' => 'micro',
                    'attiva' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Generate token
            $token = Str::random(64);
            DB::table('aa_tokens')->insert([
                'user_id' => $userId,
                'token' => hash('sha256', $token),
                'name' => 'auth',
                'expires_at' => now()->addDays(30),
                'created_at' => now(),
            ]);

            // If referred by someone, credit them with +3 months
            if ($referredBy) {
                $referrer = DB::table('aa_users')->where('id', $referredBy)->first();
                $newStorico = min(9, ($referrer->storico_mesi_extra ?? 0) + 3);
                $newCount = ($referrer->referral_count ?? 0) + 1;

                DB::table('aa_users')->where('id', $referredBy)->update([
                    'storico_mesi_extra' => $newStorico,
                    'referral_count' => $newCount,
                    'updated_at' => now(),
                ]);

                DB::table('aa_referrals')->insert([
                    'referrer_id' => $referredBy,
                    'referred_id' => $userId,
                    'created_at' => now(),
                ]);
            }

            // If registering via invite, create the client-commercialista link
            if ($inviteRecord) {
                if ($inviteRecord->tipo === 'commercialista_to_client' && $tipoUtente === 'imprenditore') {
                    // Commercialista invited this client → link client to commercialista
                    DB::table('aa_client_commercialista')->insert([
                        'client_user_id' => $userId,
                        'commercialista_user_id' => $inviteRecord->sender_user_id,
                        'azienda_id' => $aziendaId,
                        'stato' => 'active',
                        'invited_by' => 'commercialista',
                        'linked_at' => now(),
                        'created_at' => now(),
                    ]);
                } elseif ($inviteRecord->tipo === 'client_to_commercialista' && $tipoUtente === 'consulente') {
                    // Client invited this commercialista → link commercialista to client
                    DB::table('aa_client_commercialista')->insert([
                        'client_user_id' => $inviteRecord->sender_user_id,
                        'commercialista_user_id' => $userId,
                        'azienda_id' => $inviteRecord->azienda_id,
                        'stato' => 'active',
                        'invited_by' => 'client',
                        'linked_at' => now(),
                        'created_at' => now(),
                    ]);
                }

                // Mark invite as accepted
                DB::table('aa_inviti_bidirezionali')
                    ->where('id', $inviteRecord->id)
                    ->update([
                        'stato' => 'accepted',
                        'recipient_user_id' => $userId,
                        'accepted_at' => now(),
                    ]);
            }

            $user = DB::table('aa_users')->where('id', $userId)->first();

            DB::commit();

            $responseData = [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'piano' => $user->piano,
                    'tipo_utente' => $user->tipo_utente,
                    'referral_code' => $user->referral_code,
                    'trial_ends_at' => $user->trial_ends_at,
                ]
            ];

            if ($tipoUtente === 'consulente') {
                $responseData['studio_id'] = $studioId;
            } else {
                $responseData['azienda_id'] = $aziendaId;
            }

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la registrazione: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = DB::table('aa_users')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenziali non valide'
            ], 401);
        }

        $tipoUtente = $user->tipo_utente ?? 'imprenditore';
        // Note: consulenti (commercialisti) are now free, no trial block needed

        // Generate new token
        $token = Str::random(64);
        DB::table('aa_tokens')->insert([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'name' => 'auth',
            'expires_at' => now()->addDays(30),
            'created_at' => now(),
        ]);

        $responseData = [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nome' => $user->nome,
                'cognome' => $user->cognome,
                'email' => $user->email,
                'piano' => $user->piano,
                'tipo_utente' => $tipoUtente,
                'trial_ends_at' => $user->trial_ends_at,
            ]
        ];

        // Add context based on user type
        if ($tipoUtente === 'consulente') {
            $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
            $responseData['user']['studio_id'] = $studio ? $studio->id : null;
            $responseData['user']['studio_nome'] = $studio ? $studio->nome : null;
        } elseif ($tipoUtente === 'cliente_readonly') {
            $azienda = DB::table('aa_aziende_cliente')->where('id', $user->azienda_cliente_id)->first();
            $responseData['user']['azienda_cliente_id'] = $azienda ? $azienda->id : null;
            $responseData['user']['azienda_cliente_nome'] = $azienda ? $azienda->nome : null;
        } else {
            // imprenditore
            $azienda = DB::table('aa_aziende')
                ->where('user_id', $user->id)
                ->where('attiva', true)
                ->first();
            $responseData['user']['azienda_id'] = $azienda ? $azienda->id : null;
            $responseData['user']['azienda_nome'] = $azienda ? $azienda->nome : null;
        }

        return response()->json([
            'success' => true,
            'data' => $responseData
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if ($token) {
            DB::table('aa_tokens')
                ->where('token', hash('sha256', $token))
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout effettuato'
        ]);
    }

    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token mancante'], 401);
        }

        $tokenRecord = DB::table('aa_tokens')
            ->where('token', hash('sha256', $token))
            ->where(function($q) {
                $q->whereNull('expires_at')
                   ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$tokenRecord) {
            return response()->json(['success' => false, 'message' => 'Token non valido'], 401);
        }

        $user = DB::table('aa_users')->where('id', $tokenRecord->user_id)->first();
        $tipoUtente = $user->tipo_utente ?? 'imprenditore';

        // Get plan features
        $piano = DB::table('aa_piani')->where('codice', $user->piano)->first();
        $features = $piano ? json_decode($piano->features ?? '{}', true) : [];

        $responseData = [
            'user' => [
                'id' => $user->id,
                'nome' => $user->nome,
                'cognome' => $user->cognome,
                'email' => $user->email,
                'piano' => $user->piano,
                'tipo_utente' => $tipoUtente,
                'trial_ends_at' => $user->trial_ends_at,
                'referral_code' => $user->referral_code,
            ],
            'features' => $features
        ];

        // Add context based on user type
        if ($tipoUtente === 'consulente') {
            $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
            $responseData['studio'] = $studio;

            // Get linked clients (new business model v2)
            $clientiLinkati = DB::table('aa_client_commercialista as cc')
                ->join('aa_users as u', 'u.id', '=', 'cc.client_user_id')
                ->join('aa_aziende as a', 'a.id', '=', 'cc.azienda_id')
                ->where('cc.commercialista_user_id', $user->id)
                ->where('cc.stato', 'active')
                ->select('cc.id as link_id', 'u.id as client_id', 'u.nome', 'u.cognome', 'u.email',
                         'u.piano as client_piano', 'a.id as azienda_id', 'a.nome as azienda_nome',
                         'cc.linked_at')
                ->get();
            $responseData['clienti_linkati'] = $clientiLinkati;

            // Legacy: also include aziende_cliente for backward compat
            $aziendeCliente = DB::table('aa_aziende_cliente')
                ->where('studio_id', $studio->id ?? 0)
                ->where('attiva', true)
                ->get();
            $responseData['aziende_cliente'] = $aziendeCliente;

            // Credit summary
            $creditiMeseCorrente = DB::table('aa_crediti_commercialista')
                ->where('commercialista_user_id', $user->id)
                ->where('credito_valido', true)
                ->sum('importo_credito');
            $creditiPending = DB::table('aa_crediti_commercialista')
                ->where('commercialista_user_id', $user->id)
                ->where('credito_valido', true)
                ->whereIn('stato', ['calcolato', 'confermato'])
                ->sum('importo_credito');
            $creditiPagati = DB::table('aa_crediti_commercialista')
                ->where('commercialista_user_id', $user->id)
                ->where('stato', 'pagato')
                ->sum('importo_credito');
            $responseData['crediti'] = [
                'totale' => $creditiMeseCorrente,
                'pending' => $creditiPending,
                'pagati' => $creditiPagati,
            ];
        } elseif ($tipoUtente === 'cliente_readonly') {
            // Legacy cliente_readonly
            $aziendaCliente = DB::table('aa_aziende_cliente')
                ->where('id', $user->azienda_cliente_id)
                ->first();
            $responseData['azienda_cliente'] = $aziendaCliente;
        } else {
            // imprenditore
            $aziende = DB::table('aa_aziende')->where('user_id', $user->id)->get();
            $responseData['aziende'] = $aziende;

            // Check if linked to a commercialista
            $linkComm = DB::table('aa_client_commercialista as cc')
                ->join('aa_users as u', 'u.id', '=', 'cc.commercialista_user_id')
                ->where('cc.client_user_id', $user->id)
                ->where('cc.stato', 'active')
                ->select('u.id as commercialista_id', 'u.nome', 'u.cognome', 'u.email')
                ->first();
            $responseData['commercialista'] = $linkComm;
        }

        return response()->json([
            'success' => true,
            'data' => $responseData
        ]);
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        // Always return success to prevent email enumeration
        $user = DB::table('aa_users')->where('email', $request->email)->first();

        if ($user) {
            $token = Str::random(64);

            DB::table('aa_password_resets')->updateOrInsert(
                ['email' => $request->email],
                ['token' => hash('sha256', $token), 'created_at' => now()]
            );

            // TODO: Send email with reset link
        }

        return response()->json([
            'success' => true,
            'message' => 'Se l\'email esiste, riceverai un link di reset'
        ]);
    }
}
