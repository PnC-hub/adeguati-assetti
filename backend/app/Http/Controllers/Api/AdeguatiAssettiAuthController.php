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
            // Imprenditore: free, Consulente: business (30-day trial)
            $piano = $tipoUtente === 'consulente' ? 'business' : 'free';
            $trialEndsAt = $tipoUtente === 'consulente' ? now()->addDays(30) : null;

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

        // Check if consulente trial has expired and account is frozen
        $tipoUtente = $user->tipo_utente ?? 'imprenditore';
        if ($tipoUtente === 'consulente' && $user->trial_ends_at && now()->greaterThan($user->trial_ends_at)) {
            // Check if they have an active subscription
            if (!$user->stripe_subscription_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Il tuo periodo di prova è scaduto. Effettua l\'upgrade per continuare.',
                    'trial_expired' => true,
                    'upgrade_required' => true
                ], 403);
            }
        }

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
            $aziendeCliente = DB::table('aa_aziende_cliente')
                ->where('studio_id', $studio->id ?? 0)
                ->where('attiva', true)
                ->get();
            $responseData['studio'] = $studio;
            $responseData['aziende_cliente'] = $aziendeCliente;
            $responseData['max_aziende'] = $piano->max_aziende ?? 0;

            // Check trial status
            if ($user->trial_ends_at) {
                $responseData['trial_active'] = now()->lessThan($user->trial_ends_at);
                $responseData['trial_days_left'] = max(0, now()->diffInDays($user->trial_ends_at, false));
            }
        } elseif ($tipoUtente === 'cliente_readonly') {
            $aziendaCliente = DB::table('aa_aziende_cliente')
                ->where('id', $user->azienda_cliente_id)
                ->first();
            $responseData['azienda_cliente'] = $aziendaCliente;
        } else {
            // imprenditore
            $aziende = DB::table('aa_aziende')->where('user_id', $user->id)->get();
            $responseData['aziende'] = $aziende;
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
