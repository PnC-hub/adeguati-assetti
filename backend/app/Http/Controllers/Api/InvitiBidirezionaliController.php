<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitiBidirezionaliController extends Controller
{
    /**
     * Send invite (bidirectional)
     * - Imprenditore sends to commercialista: {email, azienda_id}
     * - Consulente sends to client: {email}
     */
    public function store(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'azienda_id' => 'nullable|integer',
        ]);

        // Determine invite type based on sender's user type
        if ($user->tipo_utente === 'imprenditore') {
            // Client invites commercialista
            $aziendaId = $validated['azienda_id'] ?? null;
            if (!$aziendaId) {
                // Default to first active azienda
                $azienda = DB::table('aa_aziende')
                    ->where('user_id', $user->id)
                    ->where('attiva', true)
                    ->first();
                $aziendaId = $azienda ? $azienda->id : null;
            } else {
                $azienda = DB::table('aa_aziende')
                    ->where('id', $aziendaId)
                    ->where('user_id', $user->id)
                    ->first();
            }

            if (!$aziendaId || !$azienda) {
                return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
            }

            // Check if already linked to a commercialista
            $existingLink = DB::table('aa_client_commercialista')
                ->where('client_user_id', $user->id)
                ->where('azienda_id', $aziendaId)
                ->where('stato', 'active')
                ->first();

            if ($existingLink) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hai già un commercialista collegato per questa azienda'
                ], 409);
            }

            $tipo = 'client_to_commercialista';
            $studioId = null;

        } elseif ($user->tipo_utente === 'consulente') {
            // Commercialista invites client
            $tipo = 'commercialista_to_client';
            $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
            $studioId = $studio ? $studio->id : null;
            $aziendaId = null;
        } else {
            return response()->json(['success' => false, 'message' => 'Tipo utente non autorizzato'], 403);
        }

        // Check for existing pending invite
        $existing = DB::table('aa_inviti_bidirezionali')
            ->where('sender_user_id', $user->id)
            ->where('recipient_email', $validated['email'])
            ->where('stato', 'pending')
            ->where('scade_at', '>', now())
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Invito già inviato a questo indirizzo email'
            ], 409);
        }

        $token = Str::random(64);

        $id = DB::table('aa_inviti_bidirezionali')->insertGetId([
            'tipo' => $tipo,
            'sender_user_id' => $user->id,
            'recipient_email' => $validated['email'],
            'azienda_id' => $aziendaId,
            'studio_id' => $studioId,
            'token' => $token,
            'stato' => 'pending',
            'scade_at' => now()->addDays(7),
            'created_at' => now(),
        ]);

        // If the recipient already has an account, we could auto-link
        $recipientUser = DB::table('aa_users')->where('email', $validated['email'])->first();
        $hasAccount = $recipientUser !== null;

        $invito = DB::table('aa_inviti_bidirezionali')->find($id);
        $frontendUrl = config('app.frontend_url', 'https://adeguatiassettiimpresa.it');

        return response()->json([
            'success' => true,
            'data' => $invito,
            'recipient_has_account' => $hasAccount,
            'invite_url' => $frontendUrl . '/invite-v2/' . $token,
        ], 201);
    }

    /**
     * List sent invites
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $inviti = DB::table('aa_inviti_bidirezionali')
            ->where('sender_user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $inviti]);
    }

    /**
     * List received invites (pending)
     */
    public function ricevuti(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $inviti = DB::table('aa_inviti_bidirezionali as i')
            ->leftJoin('aa_users as sender', 'sender.id', '=', 'i.sender_user_id')
            ->leftJoin('aa_aziende as a', 'a.id', '=', 'i.azienda_id')
            ->leftJoin('aa_studi as s', 's.id', '=', 'i.studio_id')
            ->where('i.recipient_email', $user->email)
            ->where('i.stato', 'pending')
            ->where('i.scade_at', '>', now())
            ->select('i.*', 'sender.nome as sender_nome', 'sender.cognome as sender_cognome',
                     'a.nome as azienda_nome', 's.nome as studio_nome')
            ->orderBy('i.created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $inviti]);
    }

    /**
     * Get invite info (public endpoint)
     */
    public function info($token): JsonResponse
    {
        $invito = DB::table('aa_inviti_bidirezionali as i')
            ->leftJoin('aa_users as sender', 'sender.id', '=', 'i.sender_user_id')
            ->leftJoin('aa_aziende as a', 'a.id', '=', 'i.azienda_id')
            ->leftJoin('aa_studi as s', 's.id', '=', 'i.studio_id')
            ->where('i.token', $token)
            ->where('i.stato', 'pending')
            ->where('i.scade_at', '>', now())
            ->select('i.tipo', 'i.recipient_email', 'i.scade_at',
                     'sender.nome as sender_nome', 'sender.cognome as sender_cognome',
                     'a.nome as azienda_nome', 's.nome as studio_nome')
            ->first();

        if (!$invito) {
            return response()->json([
                'success' => false,
                'message' => 'Invito non valido, scaduto o già utilizzato'
            ], 404);
        }

        // Check if recipient already has an account
        $hasAccount = DB::table('aa_users')
            ->where('email', $invito->recipient_email)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'tipo' => $invito->tipo,
                'email' => $invito->recipient_email,
                'sender_nome' => $invito->sender_nome . ' ' . $invito->sender_cognome,
                'azienda_nome' => $invito->azienda_nome,
                'studio_nome' => $invito->studio_nome,
                'scade_at' => $invito->scade_at,
                'has_account' => $hasAccount,
            ]
        ]);
    }

    /**
     * Accept invite (public endpoint)
     * - New user: creates account + link
     * - Existing user: just creates link
     */
    public function accept(Request $request, $token): JsonResponse
    {
        $invito = DB::table('aa_inviti_bidirezionali')
            ->where('token', $token)
            ->where('stato', 'pending')
            ->where('scade_at', '>', now())
            ->first();

        if (!$invito) {
            return response()->json([
                'success' => false,
                'message' => 'Invito non valido, scaduto o già utilizzato'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $recipientUser = DB::table('aa_users')
                ->where('email', $invito->recipient_email)
                ->first();

            $authToken = null;
            $aziendaId = $invito->azienda_id;

            if ($recipientUser) {
                // Existing user: just create the link
                $userId = $recipientUser->id;

                if ($invito->tipo === 'commercialista_to_client') {
                    // Client already exists, get their azienda
                    $azienda = DB::table('aa_aziende')
                        ->where('user_id', $userId)
                        ->where('attiva', true)
                        ->first();
                    $aziendaId = $azienda ? $azienda->id : null;

                    if (!$aziendaId) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'L\'utente non ha un\'azienda attiva'
                        ], 400);
                    }
                }
            } else {
                // New user: requires registration data
                $rules = [
                    'nome' => 'required|string|max:100',
                    'cognome' => 'required|string|max:100',
                    'password' => 'required|string|min:8',
                ];

                if ($invito->tipo === 'commercialista_to_client') {
                    $rules['azienda'] = 'required|string|max:255';
                } elseif ($invito->tipo === 'client_to_commercialista') {
                    $rules['studio_nome'] = 'required|string|max:255';
                    $rules['studio_p_iva'] = 'nullable|string|max:20';
                }

                $validated = $request->validate($rules);

                if ($invito->tipo === 'commercialista_to_client') {
                    // Create imprenditore account
                    $userId = DB::table('aa_users')->insertGetId([
                        'nome' => $validated['nome'],
                        'cognome' => $validated['cognome'],
                        'email' => $invito->recipient_email,
                        'password' => bcrypt($validated['password']),
                        'tipo_utente' => 'imprenditore',
                        'piano' => 'free',
                        'referral_code' => strtoupper(Str::random(8)),
                        'storico_mesi_extra' => 0,
                        'referral_count' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $aziendaId = DB::table('aa_aziende')->insertGetId([
                        'user_id' => $userId,
                        'nome' => $validated['azienda'],
                        'dimensione' => 'micro',
                        'attiva' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                } elseif ($invito->tipo === 'client_to_commercialista') {
                    // Create consulente account
                    $userId = DB::table('aa_users')->insertGetId([
                        'nome' => $validated['nome'],
                        'cognome' => $validated['cognome'],
                        'email' => $invito->recipient_email,
                        'password' => bcrypt($validated['password']),
                        'tipo_utente' => 'consulente',
                        'piano' => 'commercialista_free',
                        'referral_code' => strtoupper(Str::random(8)),
                        'storico_mesi_extra' => 0,
                        'referral_count' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $studioId = DB::table('aa_studi')->insertGetId([
                        'user_id' => $userId,
                        'nome' => $validated['studio_nome'],
                        'p_iva' => $validated['studio_p_iva'] ?? null,
                        'email' => $invito->recipient_email,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('aa_users')->where('id', $userId)->update(['studio_id' => $studioId]);
                }

                // Generate auth token for new user
                $authToken = Str::random(64);
                DB::table('aa_tokens')->insert([
                    'user_id' => $userId,
                    'token' => hash('sha256', $authToken),
                    'name' => 'auth',
                    'expires_at' => now()->addDays(30),
                    'created_at' => now(),
                ]);
            }

            // Create the client-commercialista link
            if ($invito->tipo === 'commercialista_to_client') {
                $clientId = $recipientUser ? $recipientUser->id : $userId;
                $commercialistaId = $invito->sender_user_id;
            } else {
                $clientId = $invito->sender_user_id;
                $commercialistaId = $recipientUser ? $recipientUser->id : $userId;
            }

            // Only create link if not already exists
            $existingLink = DB::table('aa_client_commercialista')
                ->where('client_user_id', $clientId)
                ->where('commercialista_user_id', $commercialistaId)
                ->where('azienda_id', $aziendaId)
                ->first();

            if (!$existingLink) {
                DB::table('aa_client_commercialista')->insert([
                    'client_user_id' => $clientId,
                    'commercialista_user_id' => $commercialistaId,
                    'azienda_id' => $aziendaId,
                    'stato' => 'active',
                    'invited_by' => $invito->tipo === 'client_to_commercialista' ? 'client' : 'commercialista',
                    'linked_at' => now(),
                    'created_at' => now(),
                ]);
            } elseif ($existingLink->stato === 'terminated') {
                DB::table('aa_client_commercialista')
                    ->where('id', $existingLink->id)
                    ->update([
                        'stato' => 'active',
                        'linked_at' => now(),
                        'terminated_at' => null,
                    ]);
            }

            // Mark invite as accepted
            DB::table('aa_inviti_bidirezionali')
                ->where('id', $invito->id)
                ->update([
                    'stato' => 'accepted',
                    'recipient_user_id' => $recipientUser ? $recipientUser->id : $userId,
                    'accepted_at' => now(),
                ]);

            DB::commit();

            $user = DB::table('aa_users')->find($recipientUser ? $recipientUser->id : $userId);

            $responseData = [
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'tipo_utente' => $user->tipo_utente,
                    'piano' => $user->piano,
                ],
                'redirect' => $user->tipo_utente === 'consulente' ? '/consulente' : '/dashboard',
            ];

            if ($authToken) {
                $responseData['token'] = $authToken;
            }

            return response()->json(['success' => true, 'data' => $responseData]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revoke invite
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $invito = DB::table('aa_inviti_bidirezionali')
            ->where('id', $id)
            ->where('sender_user_id', $user->id)
            ->where('stato', 'pending')
            ->first();

        if (!$invito) {
            return response()->json(['success' => false, 'message' => 'Invito non trovato'], 404);
        }

        DB::table('aa_inviti_bidirezionali')
            ->where('id', $id)
            ->update(['stato' => 'revoked']);

        return response()->json(['success' => true, 'message' => 'Invito revocato']);
    }

    private function getAuthUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) return null;

        $tokenRecord = DB::table('aa_tokens')
            ->where('token', hash('sha256', $token))
            ->where(function($q) {
                $q->whereNull('expires_at')
                   ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$tokenRecord) return null;

        return DB::table('aa_users')->where('id', $tokenRecord->user_id)->first();
    }
}
