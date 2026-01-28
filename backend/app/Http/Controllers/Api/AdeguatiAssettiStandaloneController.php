<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdeguatiAssettiStandaloneController extends Controller
{
    /**
     * Dashboard per utente standalone
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $aziendaId = $request->get('azienda_id');
        $anno = $request->get('anno', now()->year);
        $mese = $request->get('mese', now()->month);

        // Verifica azienda appartiene all'utente
        $azienda = DB::table('aa_aziende')
            ->where('id', $aziendaId)
            ->where('user_id', $user->id)
            ->first();

        if (!$azienda && $aziendaId) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
        }

        // Se non specificata, prendi la prima azienda
        if (!$azienda) {
            $azienda = DB::table('aa_aziende')
                ->where('user_id', $user->id)
                ->first();
        }

        if (!$azienda) {
            return response()->json(['success' => false, 'message' => 'Nessuna azienda configurata'], 400);
        }

        // Recupera KPI calcolati
        $kpiValori = DB::table('aa_kpi_valori as v')
            ->join('aa_kpi_definizioni as d', 'v.kpi_codice', '=', 'd.codice')
            ->where('v.azienda_id', $azienda->id)
            ->where('v.anno', $anno)
            ->where('v.mese', $mese)
            ->select('d.*', 'v.valore', 'v.stato', 'v.delta_precedente')
            ->get();

        // Recupera alert attivi
        $alert = DB::table('aa_alert')
            ->where('azienda_id', $azienda->id)
            ->where('anno', $anno)
            ->where('mese', $mese)
            ->where('letto', false)
            ->orderBy('livello', 'desc')
            ->get();

        // Calcola score
        $score = $this->calcolaScore($kpiValori);

        return response()->json([
            'success' => true,
            'data' => [
                'azienda' => $azienda,
                'periodo' => [
                    'anno' => $anno,
                    'mese' => $mese,
                    'label' => Carbon::create($anno, $mese)->locale('it')->translatedFormat('F Y'),
                ],
                'score' => $score,
                'stato_generale' => $score >= 70 ? 'buono' : ($score >= 50 ? 'attenzione' : 'critico'),
                'kpi_obbligatori' => $kpiValori->where('categoria', 'obbligatorio')->values(),
                'kpi_settoriali' => $kpiValori->where('categoria', 'settoriale')->values(),
                'kpi_operativi' => $kpiValori->where('categoria', 'operativo')->values(),
                'alert' => $alert,
                'alert_count' => $alert->count(),
            ]
        ]);
    }

    /**
     * Get dati economici per anno/mese
     */
    public function getDatiEconomici(Request $request, int $anno, int $mese): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        // Get user's azienda
        $azienda = DB::table('aa_aziende')
            ->where('user_id', $user->id)
            ->first();

        if (!$azienda) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
        }

        $dati = DB::table('aa_dati_economici')
            ->where('azienda_id', $azienda->id)
            ->where('anno', $anno)
            ->where('mese', $mese)
            ->first();

        if (!$dati) {
            return response()->json(['success' => false, 'message' => 'Dati non trovati'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $dati
        ]);
    }

    /**
     * Salva dati economici
     */
    public function salvaDatiEconomici(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $request->validate([
            'azienda_id' => 'required|integer',
            'anno' => 'required|integer|min:2020|max:2030',
            'mese' => 'required|integer|min:1|max:12',
        ]);

        // Verifica azienda
        $azienda = DB::table('aa_aziende')
            ->where('id', $request->azienda_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$azienda) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
        }

        // Salva o aggiorna dati
        DB::table('aa_dati_economici')->updateOrInsert(
            [
                'azienda_id' => $azienda->id,
                'anno' => $request->anno,
                'mese' => $request->mese,
            ],
            [
                'totale_ricavi' => $request->totale_ricavi,
                'costi_materie_prime' => $request->costi_materie_prime,
                'costi_servizi' => $request->costi_servizi,
                'costi_personale' => $request->costi_personale,
                'altri_costi_operativi' => $request->altri_costi_operativi,
                'oneri_finanziari' => $request->oneri_finanziari,
                'patrimonio_netto' => $request->patrimonio_netto,
                'debiti_totali' => $request->debiti_totali,
                'debiti_breve_termine' => $request->debiti_breve_termine,
                'debiti_tributari' => $request->debiti_tributari,
                'totale_attivo' => $request->totale_attivo,
                'attivo_circolante' => $request->attivo_circolante,
                'crediti_vs_clienti' => $request->crediti_vs_clienti,
                'updated_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Dati salvati con successo'
        ]);
    }

    /**
     * Calcola KPI dal dati economici
     */
    public function calcolaKpi(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $request->validate([
            'azienda_id' => 'required|integer',
            'anno' => 'required|integer',
            'mese' => 'required|integer|min:1|max:12',
        ]);

        $azienda = DB::table('aa_aziende')
            ->where('id', $request->azienda_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$azienda) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
        }

        // Recupera dati economici
        $dati = DB::table('aa_dati_economici')
            ->where('azienda_id', $azienda->id)
            ->where('anno', $request->anno)
            ->where('mese', $request->mese)
            ->first();

        if (!$dati) {
            return response()->json(['success' => false, 'message' => 'Inserire prima i dati economici'], 400);
        }

        // Recupera definizioni KPI
        $kpiDefs = DB::table('aa_kpi_definizioni')->where('attivo', true)->get();

        $risultati = [];

        foreach ($kpiDefs as $kpi) {
            $valore = $this->calcolaValore($kpi->codice, $dati);
            $stato = $this->determinaStato($valore, $kpi);

            // Salva valore
            DB::table('aa_kpi_valori')->updateOrInsert(
                [
                    'azienda_id' => $azienda->id,
                    'kpi_codice' => $kpi->codice,
                    'anno' => $request->anno,
                    'mese' => $request->mese,
                ],
                [
                    'valore' => $valore,
                    'stato' => $stato,
                    'calcolato_il' => now(),
                ]
            );

            // Genera alert se critico
            if ($stato === 'rosso' || $stato === 'giallo') {
                DB::table('aa_alert')->updateOrInsert(
                    [
                        'azienda_id' => $azienda->id,
                        'kpi_codice' => $kpi->codice,
                        'anno' => $request->anno,
                        'mese' => $request->mese,
                    ],
                    [
                        'livello' => $stato === 'rosso' ? 'critical' : 'warning',
                        'messaggio' => $this->getMessaggioAlert($kpi, $valore),
                        'azione_suggerita' => $this->getAzioneSuggerita($kpi->codice),
                        'letto' => false,
                        'created_at' => now(),
                    ]
                );
            }

            $risultati[] = [
                'codice' => $kpi->codice,
                'nome' => $kpi->nome,
                'valore' => $valore,
                'stato' => $stato,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $risultati
        ]);
    }

    /**
     * Lista alert
     */
    public function listaAlert(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $aziendaId = $request->get('azienda_id');

        $query = DB::table('aa_alert as a')
            ->join('aa_aziende as az', 'a.azienda_id', '=', 'az.id')
            ->where('az.user_id', $user->id);

        if ($aziendaId) {
            $query->where('a.azienda_id', $aziendaId);
        }

        $alert = $query->orderBy('a.created_at', 'desc')
            ->select('a.*', 'az.nome as azienda_nome')
            ->limit(50)
            ->get();

        return response()->json(['success' => true, 'data' => $alert]);
    }

    /**
     * Export dati
     */
    public function exportDati(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $aziendaId = $request->get('azienda_id');
        $anno = $request->get('anno', now()->year);

        $azienda = DB::table('aa_aziende')
            ->where('id', $aziendaId)
            ->where('user_id', $user->id)
            ->first();

        if (!$azienda) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
        }

        // Recupera tutti i dati dell'anno
        $datiEconomici = DB::table('aa_dati_economici')
            ->where('azienda_id', $azienda->id)
            ->where('anno', $anno)
            ->orderBy('mese')
            ->get();

        $kpiValori = DB::table('aa_kpi_valori as v')
            ->join('aa_kpi_definizioni as d', 'v.kpi_codice', '=', 'd.codice')
            ->where('v.azienda_id', $azienda->id)
            ->where('v.anno', $anno)
            ->orderBy('v.mese')
            ->select('v.*', 'd.nome', 'd.categoria')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'azienda' => $azienda,
                'anno' => $anno,
                'dati_economici' => $datiEconomici,
                'kpi_valori' => $kpiValori,
            ]
        ]);
    }

    // === HELPER METHODS ===

    private function getAuthUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) return null;

        $tokenRecord = DB::table('aa_tokens')
            ->where('token', hash('sha256', $token))
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$tokenRecord) return null;

        return DB::table('aa_users')->where('id', $tokenRecord->user_id)->first();
    }

    private function calcolaValore(string $codice, $dati): ?float
    {
        switch ($codice) {
            case 'PN':
                return $dati->patrimonio_netto ?? null;

            case 'DSCR':
                $cashFlow = ($dati->totale_ricavi ?? 0) - ($dati->costi_materie_prime ?? 0)
                    - ($dati->costi_servizi ?? 0) - ($dati->costi_personale ?? 0);
                return ($dati->debiti_breve_termine ?? 0) > 0
                    ? $cashFlow / $dati->debiti_breve_termine : null;

            case 'CR':
                return ($dati->debiti_breve_termine ?? 0) > 0
                    ? ($dati->attivo_circolante ?? 0) / $dati->debiti_breve_termine : null;

            case 'OF_RIC':
                return ($dati->totale_ricavi ?? 0) > 0
                    ? (($dati->oneri_finanziari ?? 0) / $dati->totale_ricavi) * 100 : null;

            case 'PN_DEB':
                return ($dati->debiti_totali ?? 0) > 0
                    ? ($dati->patrimonio_netto ?? 0) / $dati->debiti_totali : null;

            case 'CF_ATT':
                $cashFlow = ($dati->totale_ricavi ?? 0) - ($dati->costi_materie_prime ?? 0)
                    - ($dati->costi_servizi ?? 0) - ($dati->costi_personale ?? 0);
                return ($dati->totale_attivo ?? 0) > 0
                    ? ($cashFlow / $dati->totale_attivo) * 100 : null;

            case 'DEBFISC_ATT':
                return ($dati->totale_attivo ?? 0) > 0
                    ? (($dati->debiti_tributari ?? 0) / $dati->totale_attivo) * 100 : null;

            case 'MARG_LORDO':
                $margine = ($dati->totale_ricavi ?? 0) - ($dati->costi_materie_prime ?? 0) - ($dati->costi_servizi ?? 0);
                return ($dati->totale_ricavi ?? 0) > 0
                    ? ($margine / $dati->totale_ricavi) * 100 : null;

            case 'DSO':
                return ($dati->totale_ricavi ?? 0) > 0
                    ? (($dati->crediti_vs_clienti ?? 0) / $dati->totale_ricavi) * 30 : null;

            case 'CPERS_FATT':
                return ($dati->totale_ricavi ?? 0) > 0
                    ? (($dati->costi_personale ?? 0) / $dati->totale_ricavi) * 100 : null;

            default:
                return null;
        }
    }

    private function determinaStato(?float $valore, $kpi): string
    {
        if ($valore === null) return 'nd';

        if ($kpi->direzione === 'maggiore') {
            if ($valore >= $kpi->soglia_verde) return 'verde';
            if ($valore >= $kpi->soglia_gialla) return 'giallo';
            return 'rosso';
        } else {
            if ($valore <= $kpi->soglia_verde) return 'verde';
            if ($valore <= $kpi->soglia_gialla) return 'giallo';
            return 'rosso';
        }
    }

    private function calcolaScore($kpiValori): int
    {
        if ($kpiValori->isEmpty()) return 0;

        $punti = 0;
        $kpiConDati = 0;

        foreach ($kpiValori as $kpi) {
            // Skip KPI without data
            if ($kpi->stato === 'nd' || $kpi->stato === null) continue;

            $kpiConDati++;
            if ($kpi->stato === 'verde') $punti += 10;
            elseif ($kpi->stato === 'giallo') $punti += 5;
            // rosso = 0 punti
        }

        $max = $kpiConDati * 10;
        return $max > 0 ? round(($punti / $max) * 100) : 0;
    }

    private function getMessaggioAlert($kpi, ?float $valore): string
    {
        $valoreStr = $valore !== null ? number_format($valore, 2) : 'N/D';
        return "{$kpi->nome}: {$valoreStr} - richiede attenzione";
    }

    private function getAzioneSuggerita(string $codice): string
    {
        $azioni = [
            'DSCR' => 'Accelerare incassi, rinegoziare debiti',
            'DSO' => 'Sollecitare crediti, rivedere condizioni pagamento',
            'PN' => 'Valutare ricapitalizzazione',
            'CR' => 'Migliorare gestione liquidita',
            'OF_RIC' => 'Rinegoziare condizioni finanziarie',
        ];
        return $azioni[$codice] ?? 'Monitorare e analizzare le cause';
    }
}
