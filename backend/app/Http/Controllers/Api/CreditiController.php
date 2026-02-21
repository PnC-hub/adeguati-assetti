<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CreditiController extends Controller
{
    /**
     * List all credits for the authenticated commercialista
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $crediti = DB::table('aa_crediti_commercialista as c')
            ->join('aa_users as u', 'u.id', '=', 'c.client_user_id')
            ->join('aa_aziende as a', 'a.id', '=', 'c.azienda_id')
            ->where('c.commercialista_user_id', $user->id)
            ->select(
                'c.*',
                'u.nome as client_nome',
                'u.cognome as client_cognome',
                'u.email as client_email',
                'a.nome as azienda_nome'
            )
            ->orderBy('c.anno', 'desc')
            ->orderBy('c.mese', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $crediti]);
    }

    /**
     * Credit summary: totals by status
     */
    public function riepilogo(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $totaleGuadagnato = DB::table('aa_crediti_commercialista')
            ->where('commercialista_user_id', $user->id)
            ->where('credito_valido', true)
            ->sum('importo_credito');

        $totalePending = DB::table('aa_crediti_commercialista')
            ->where('commercialista_user_id', $user->id)
            ->where('credito_valido', true)
            ->whereIn('stato', ['calcolato', 'confermato'])
            ->sum('importo_credito');

        $totalePagato = DB::table('aa_crediti_commercialista')
            ->where('commercialista_user_id', $user->id)
            ->where('stato', 'pagato')
            ->sum('importo_credito');

        // Clients generating revenue
        $clientiAttivi = DB::table('aa_client_commercialista')
            ->where('commercialista_user_id', $user->id)
            ->where('stato', 'active')
            ->count();

        // Monthly breakdown (last 12 months)
        $mesiRecenti = DB::table('aa_crediti_commercialista')
            ->where('commercialista_user_id', $user->id)
            ->where('credito_valido', true)
            ->select(
                'anno', 'mese',
                DB::raw('SUM(importo_credito) as totale_mese'),
                DB::raw('COUNT(*) as num_clienti'),
                DB::raw('MIN(stato) as stato_mese')
            )
            ->groupBy('anno', 'mese')
            ->orderBy('anno', 'desc')
            ->orderBy('mese', 'desc')
            ->limit(12)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'totale_guadagnato' => round($totaleGuadagnato, 2),
                'totale_pending' => round($totalePending, 2),
                'totale_pagato' => round($totalePagato, 2),
                'clienti_attivi' => $clientiAttivi,
                'importo_per_cliente' => 9.80,
                'percentuale_revenue_share' => 20,
                'mesi' => $mesiRecenti,
            ]
        ]);
    }

    /**
     * Credits for a specific month
     */
    public function perMese(Request $request, int $anno, int $mese): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $crediti = DB::table('aa_crediti_commercialista as c')
            ->join('aa_users as u', 'u.id', '=', 'c.client_user_id')
            ->join('aa_aziende as a', 'a.id', '=', 'c.azienda_id')
            ->where('c.commercialista_user_id', $user->id)
            ->where('c.anno', $anno)
            ->where('c.mese', $mese)
            ->select(
                'c.*',
                'u.nome as client_nome',
                'u.cognome as client_cognome',
                'a.nome as azienda_nome'
            )
            ->get();

        $totale = $crediti->where('credito_valido', true)->sum('importo_credito');

        return response()->json([
            'success' => true,
            'data' => [
                'anno' => $anno,
                'mese' => $mese,
                'totale' => round($totale, 2),
                'crediti' => $crediti,
            ]
        ]);
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
