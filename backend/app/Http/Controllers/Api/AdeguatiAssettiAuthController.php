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
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'cognome' => 'required|string|max:100',
            'azienda' => 'required|string|max:255',
            'email' => 'required|email|unique:aa_users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $userId = DB::table('aa_users')->insertGetId([
                'nome' => $request->nome,
                'cognome' => $request->cognome,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'piano' => 'trial',
                'trial_ends_at' => now()->addDays(14),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create default company
            DB::table('aa_aziende')->insert([
                'user_id' => $userId,
                'nome' => $request->azienda,
                'dimensione' => 'micro',
                'attiva' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Generate token
            $token = Str::random(64);
            DB::table('aa_tokens')->insert([
                'user_id' => $userId,
                'token' => hash('sha256', $token),
                'name' => 'auth',
                'expires_at' => now()->addDays(30),
                'created_at' => now(),
            ]);

            $user = DB::table('aa_users')->where('id', $userId)->first();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'nome' => $user->nome,
                        'cognome' => $user->cognome,
                        'email' => $user->email,
                        'piano' => $user->piano,
                    ]
                ]
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

        // Generate new token
        $token = Str::random(64);
        DB::table('aa_tokens')->insert([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'name' => 'auth',
            'expires_at' => now()->addDays(30),
            'created_at' => now(),
        ]);

        // Get user's first azienda
        $azienda = DB::table('aa_aziende')
            ->where('user_id', $user->id)
            ->where('attiva', true)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'piano' => $user->piano,
                    'azienda_id' => $azienda ? $azienda->id : null,
                    'azienda_nome' => $azienda ? $azienda->nome : null,
                ]
            ]
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
        $aziende = DB::table('aa_aziende')->where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'piano' => $user->piano,
                    'trial_ends_at' => $user->trial_ends_at,
                ],
                'aziende' => $aziende
            ]
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
