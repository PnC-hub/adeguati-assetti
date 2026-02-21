<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommercialistaClientiController extends Controller
{
    /**
     * List all linked clients with their aziende
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $clienti = DB::table('aa_client_commercialista as cc')
            ->join('aa_users as u', 'u.id', '=', 'cc.client_user_id')
            ->join('aa_aziende as a', 'a.id', '=', 'cc.azienda_id')
            ->where('cc.commercialista_user_id', $user->id)
            ->where('cc.stato', 'active')
            ->select(
                'cc.id as link_id',
                'cc.linked_at',
                'cc.invited_by',
                'u.id as client_id',
                'u.nome',
                'u.cognome',
                'u.email',
                'u.piano as client_piano',
                'u.stripe_subscription_id',
                'a.id as azienda_id',
                'a.nome as azienda_nome',
                'a.settore',
                'a.codice_ateco',
                'a.dimensione'
            )
            ->orderBy('cc.linked_at', 'desc')
            ->get();

        // Add subscription status for each client
        $clienti = $clienti->map(function ($c) {
            $c->subscription_active = !empty($c->stripe_subscription_id) && $c->client_piano === 'impresa49';
            return $c;
        });

        return response()->json([
            'success' => true,
            'data' => $clienti,
            'totale_clienti' => $clienti->count(),
            'clienti_attivi' => $clienti->where('subscription_active', true)->count(),
        ]);
    }

    /**
     * View a specific client's dashboard (read-only)
     */
    public function clientDashboard(Request $request, int $aziendaId): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        // Verify commercialista has access to this azienda
        $link = $this->verifyAccess($user->id, $aziendaId);
        if (!$link) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata o accesso negato'], 404);
        }

        $azienda = DB::table('aa_aziende')->find($aziendaId);
        $anno = $request->get('anno', now()->year);
        $mese = $request->get('mese', now()->month);

        // Get KPI values (same data the client sees)
        $kpiValori = DB::table('aa_kpi_valori as v')
            ->join('aa_kpi_definizioni as d', 'v.kpi_codice', '=', 'd.codice')
            ->where('v.azienda_id', $aziendaId)
            ->where('v.anno', $anno)
            ->where('v.mese', $mese)
            ->select('d.*', 'v.valore', 'v.stato', 'v.delta_precedente')
            ->get();

        // Get alerts
        $alert = DB::table('aa_alert')
            ->where('azienda_id', $aziendaId)
            ->where('anno', $anno)
            ->where('mese', $mese)
            ->where('letto', false)
            ->orderBy('livello', 'desc')
            ->get();

        // Calculate score
        $score = $this->calcolaScore($kpiValori);

        // Get client info
        $client = DB::table('aa_users')->find($link->client_user_id);

        return response()->json([
            'success' => true,
            'data' => [
                'azienda' => $azienda,
                'cliente' => [
                    'nome' => $client->nome,
                    'cognome' => $client->cognome,
                    'email' => $client->email,
                    'piano' => $client->piano,
                ],
                'periodo' => [
                    'anno' => $anno,
                    'mese' => $mese,
                    'label' => Carbon::create($anno, $mese)->locale('it')->translatedFormat('F Y'),
                ],
                'score' => $score,
                'stato_generale' => $score >= 70 ? 'buono' : ($score >= 50 ? 'attenzione' : 'critico'),
                'kpi' => $kpiValori,
                'alert' => $alert,
                'alert_count' => $alert->count(),
            ]
        ]);
    }

    /**
     * View client's economic data for a specific period
     */
    public function clientDati(Request $request, int $aziendaId, int $anno, int $mese): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $link = $this->verifyAccess($user->id, $aziendaId);
        if (!$link) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata o accesso negato'], 404);
        }

        $dati = DB::table('aa_dati_economici')
            ->where('azienda_id', $aziendaId)
            ->where('anno', $anno)
            ->where('mese', $mese)
            ->first();

        if (!$dati) {
            return response()->json(['success' => false, 'message' => 'Dati non trovati per questo periodo'], 404);
        }

        return response()->json(['success' => true, 'data' => $dati]);
    }

    /**
     * Aggregated dashboard across all linked clients
     */
    public function dashboardAggregata(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $anno = $request->get('anno', now()->year);
        $mese = $request->get('mese', now()->month);

        // Get all linked aziende
        $links = DB::table('aa_client_commercialista as cc')
            ->join('aa_aziende as a', 'a.id', '=', 'cc.azienda_id')
            ->join('aa_users as u', 'u.id', '=', 'cc.client_user_id')
            ->where('cc.commercialista_user_id', $user->id)
            ->where('cc.stato', 'active')
            ->select('a.id as azienda_id', 'a.nome as azienda_nome', 'u.nome', 'u.cognome', 'u.piano')
            ->get();

        $riepilogo = [];
        $totaleScore = 0;
        $countWithScore = 0;

        foreach ($links as $link) {
            $kpiValori = DB::table('aa_kpi_valori as v')
                ->join('aa_kpi_definizioni as d', 'v.kpi_codice', '=', 'd.codice')
                ->where('v.azienda_id', $link->azienda_id)
                ->where('v.anno', $anno)
                ->where('v.mese', $mese)
                ->select('d.*', 'v.valore', 'v.stato', 'v.delta_precedente')
                ->get();

            $score = $this->calcolaScore($kpiValori);

            $riepilogo[] = [
                'azienda_id' => $link->azienda_id,
                'azienda_nome' => $link->azienda_nome,
                'cliente' => $link->nome . ' ' . $link->cognome,
                'piano' => $link->piano,
                'score' => $score,
                'stato' => $score >= 70 ? 'buono' : ($score >= 50 ? 'attenzione' : 'critico'),
                'kpi_count' => $kpiValori->count(),
                'alert_count' => $kpiValori->where('stato', 'critico')->count(),
            ];

            if ($score > 0) {
                $totaleScore += $score;
                $countWithScore++;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'periodo' => ['anno' => $anno, 'mese' => $mese],
                'totale_clienti' => count($riepilogo),
                'score_medio' => $countWithScore > 0 ? round($totaleScore / $countWithScore) : 0,
                'clienti' => $riepilogo,
            ]
        ]);
    }

    /**
     * Terminate link with a client
     */
    public function terminateLink(Request $request, int $linkId): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        // Both client and commercialista can terminate
        $link = DB::table('aa_client_commercialista')
            ->where('id', $linkId)
            ->where('stato', 'active')
            ->where(function ($q) use ($user) {
                $q->where('client_user_id', $user->id)
                  ->orWhere('commercialista_user_id', $user->id);
            })
            ->first();

        if (!$link) {
            return response()->json(['success' => false, 'message' => 'Link non trovato'], 404);
        }

        DB::table('aa_client_commercialista')
            ->where('id', $linkId)
            ->update([
                'stato' => 'terminated',
                'terminated_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Collegamento terminato']);
    }

    /**
     * List commercialisti linked to the authenticated client
     */
    public function mieiCommercialisti(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'imprenditore') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $commercialisti = DB::table('aa_client_commercialista as cc')
            ->join('aa_users as u', 'u.id', '=', 'cc.commercialista_user_id')
            ->leftJoin('aa_studi as s', 's.user_id', '=', 'u.id')
            ->where('cc.client_user_id', $user->id)
            ->where('cc.stato', 'active')
            ->select(
                'cc.id as link_id',
                'cc.linked_at',
                'u.id as commercialista_id',
                'u.nome',
                'u.cognome',
                'u.email',
                's.nome as studio_nome',
                's.p_iva as studio_p_iva'
            )
            ->get();

        return response()->json(['success' => true, 'data' => $commercialisti]);
    }

    /**
     * Verify commercialista has active access to an azienda
     */
    private function verifyAccess(int $commercialistaUserId, int $aziendaId)
    {
        return DB::table('aa_client_commercialista')
            ->where('commercialista_user_id', $commercialistaUserId)
            ->where('azienda_id', $aziendaId)
            ->where('stato', 'active')
            ->first();
    }

    /**
     * Calculate compliance score from KPI values
     */
    private function calcolaScore($kpiValori): int
    {
        if ($kpiValori->isEmpty()) return 0;

        $totale = $kpiValori->count();
        $buoni = $kpiValori->where('stato', 'buono')->count();
        $attenzione = $kpiValori->where('stato', 'attenzione')->count();

        return (int) round(($buoni * 100 + $attenzione * 50) / max($totale, 1));
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
