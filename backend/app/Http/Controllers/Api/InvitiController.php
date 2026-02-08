<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitiController extends Controller
{
    /**
     * List all inviti for current studio
     */
    public function index(Request $request)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        if (!$studio) {
            return response()->json(['success' => false, 'message' => 'Studio non trovato'], 404);
        }

        $inviti = DB::table('aa_inviti_cliente as i')
            ->join('aa_aziende_cliente as a', 'i.azienda_cliente_id', '=', 'a.id')
            ->where('a.studio_id', $studio->id)
            ->select('i.*', 'a.nome as azienda_nome')
            ->orderBy('i.created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $inviti]);
    }

    /**
     * Send invite to client
     */
    public function store(Request $request, $aziendaId)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        $azienda = DB::table('aa_aziende_cliente')->find($aziendaId);

        if (!$azienda || $azienda->studio_id !== $studio->id) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
        }

        // Check piano feature
        $piano = $this->getUserPlan($user);
        $features = json_decode($piano->features ?? '[]', true);
        if (!in_array('invita_clienti', $features)) {
            return response()->json([
                'success' => false,
                'message' => 'Funzionalità non disponibile per il tuo piano. Effettua l\'upgrade a Business.'
            ], 403);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Check if already invited
        $existing = DB::table('aa_inviti_cliente')
            ->where('azienda_cliente_id', $aziendaId)
            ->where('email', $validated['email'])
            ->where('stato', 'pending')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Invito già inviato a questo indirizzo email'
            ], 409);
        }

        $token = Str::random(64);

        $id = DB::table('aa_inviti_cliente')->insertGetId([
            'azienda_cliente_id' => $aziendaId,
            'email' => $validated['email'],
            'token' => $token,
            'stato' => 'pending',
            'scade_at' => now()->addDays(7),
            'created_at' => now(),
        ]);

        // TODO: Send email with invite link
        // Mail::to($validated['email'])->send(new InviteClienteMail($azienda, $token, $studio));

        $invito = DB::table('aa_inviti_cliente')->find($id);

        return response()->json([
            'success' => true,
            'data' => $invito,
            'invite_url' => config('app.frontend_url', 'https://adeguatiassettiimpresa.it') . '/invite/' . $token
        ], 201);
    }

    /**
     * Accept invite
     */
    public function accept(Request $request, $token)
    {
        $invito = DB::table('aa_inviti_cliente')
            ->where('token', $token)
            ->where('stato', 'pending')
            ->first();

        if (!$invito) {
            return response()->json([
                'success' => false,
                'message' => 'Invito non valido o già utilizzato'
            ], 404);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'nome' => 'required|string|max:255',
            'cognome' => 'nullable|string|max:255',
        ]);

        // Check email matches
        if (strtolower($validated['email']) !== strtolower($invito->email)) {
            return response()->json([
                'success' => false,
                'message' => 'L\'email non corrisponde all\'invito'
            ], 400);
        }

        // Check if email already exists
        $existingUser = DB::table('aa_users')->where('email', $validated['email'])->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'Email già registrata'
            ], 409);
        }

        // Create user
        $userId = DB::table('aa_users')->insertGetId([
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'nome' => $validated['nome'],
            'cognome' => $validated['cognome'] ?? null,
            'tipo_utente' => 'cliente_readonly',
            'azienda_cliente_id' => $invito->azienda_cliente_id,
            'piano' => 'free',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update invite
        DB::table('aa_inviti_cliente')
            ->where('id', $invito->id)
            ->update([
                'stato' => 'accepted',
                'user_id' => $userId,
                'accepted_at' => now(),
            ]);

        // Generate token
        $authToken = Str::random(60);
        DB::table('aa_users')
            ->where('id', $userId)
            ->update(['remember_token' => $authToken]);

        $user = DB::table('aa_users')->find($userId);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $authToken,
                'redirect' => '/cliente'
            ]
        ]);
    }

    /**
     * Revoke invite
     */
    public function destroy(Request $request, $id)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();

        $invito = DB::table('aa_inviti_cliente as i')
            ->join('aa_aziende_cliente as a', 'i.azienda_cliente_id', '=', 'a.id')
            ->where('i.id', $id)
            ->where('a.studio_id', $studio->id)
            ->select('i.*')
            ->first();

        if (!$invito) {
            return response()->json(['success' => false, 'message' => 'Invito non trovato'], 404);
        }

        DB::table('aa_inviti_cliente')
            ->where('id', $id)
            ->update(['stato' => 'revoked']);

        return response()->json(['success' => true, 'message' => 'Invito revocato']);
    }

    /**
     * Get invite info (public)
     */
    public function info($token)
    {
        $invito = DB::table('aa_inviti_cliente as i')
            ->join('aa_aziende_cliente as a', 'i.azienda_cliente_id', '=', 'a.id')
            ->join('aa_studi as s', 'a.studio_id', '=', 's.id')
            ->where('i.token', $token)
            ->where('i.stato', 'pending')
            ->select('i.email', 'a.nome as azienda_nome', 's.nome as studio_nome')
            ->first();

        if (!$invito) {
            return response()->json([
                'success' => false,
                'message' => 'Invito non valido o già utilizzato'
            ], 404);
        }

        return response()->json(['success' => true, 'data' => $invito]);
    }

    private function getAuthUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) return null;

        // Look up hashed token in aa_tokens table
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

    private function getUserPlan($user)
    {
        return DB::table('aa_piani')->where('codice', $user->piano ?? 'free')->first();
    }
}
