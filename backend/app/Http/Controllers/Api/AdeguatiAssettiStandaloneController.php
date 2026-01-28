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
     * Soglie CNDCEC per settore ATECO
     * Formato: [OF_RIC, PN_DEB, CF_ATT, CR, DEBFISC_ATT]
     */
    private const SOGLIE_SETTORIALI = [
        'A' => ['of_ric' => 2.8, 'pn_deb' => 9.4, 'cf_att' => 0.3, 'cr' => 92.1, 'debfisc_att' => 5.6],
        'B-D' => ['of_ric' => 3.0, 'pn_deb' => 7.6, 'cf_att' => 0.5, 'cr' => 93.7, 'debfisc_att' => 4.9],
        'E' => ['of_ric' => 2.6, 'pn_deb' => 6.7, 'cf_att' => 1.9, 'cr' => 84.2, 'debfisc_att' => 6.5],
        'F41' => ['of_ric' => 3.8, 'pn_deb' => 4.9, 'cf_att' => 0.4, 'cr' => 108.0, 'debfisc_att' => 3.8],
        'F42-F43' => ['of_ric' => 2.8, 'pn_deb' => 5.3, 'cf_att' => 1.4, 'cr' => 101.1, 'debfisc_att' => 5.3],
        'G45-G46' => ['of_ric' => 2.1, 'pn_deb' => 6.3, 'cf_att' => 0.6, 'cr' => 101.4, 'debfisc_att' => 2.9],
        'G47-I56' => ['of_ric' => 1.5, 'pn_deb' => 4.2, 'cf_att' => 1.0, 'cr' => 89.8, 'debfisc_att' => 7.8],
        'H-I55' => ['of_ric' => 1.5, 'pn_deb' => 4.1, 'cf_att' => 1.4, 'cr' => 86.0, 'debfisc_att' => 10.2],
        'JMN' => ['of_ric' => 1.8, 'pn_deb' => 5.2, 'cf_att' => 1.7, 'cr' => 95.4, 'debfisc_att' => 11.9],
        'PQRS' => ['of_ric' => 2.7, 'pn_deb' => 2.3, 'cf_att' => 0.5, 'cr' => 69.8, 'debfisc_att' => 14.6], // Servizi sanitari
        'DEFAULT' => ['of_ric' => 2.5, 'pn_deb' => 5.0, 'cf_att' => 1.0, 'cr' => 90.0, 'debfisc_att' => 8.0],
    ];

    /**
     * Mappa codice ATECO al gruppo settoriale CNDCEC
     */
    private function mapAtecoToSettore(?string $codiceAteco): string
    {
        if (!$codiceAteco) return 'DEFAULT';

        $prefisso = substr($codiceAteco, 0, 2);
        $prefisso2 = substr($codiceAteco, 0, 5); // es. 41.20

        // Mappatura ATECO → settore CNDCEC
        if (in_array($prefisso, ['01','02','03'])) return 'A';
        if (in_array($prefisso, ['05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','35'])) return 'B-D';
        if (in_array($prefisso, ['36','37','38','39'])) return 'E';
        if ($prefisso === '41') return 'F41';
        if (in_array($prefisso, ['42','43'])) return 'F42-F43';
        if (in_array($prefisso, ['45','46'])) return 'G45-G46';
        if ($prefisso === '47' || $prefisso === '56') return 'G47-I56';
        if (in_array($prefisso, ['49','50','51','52','53','55'])) return 'H-I55';
        if (in_array($prefisso, ['58','59','60','61','62','63','69','70','71','72','73','74','75','77','78','79','80','81','82'])) return 'JMN';
        if (in_array($prefisso, ['84','85','86','87','88','90','91','92','93','94','95','96'])) return 'PQRS';

        return 'DEFAULT';
    }

    /**
     * Lista aziende dell'utente
     */
    public function listaAziende(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $aziende = DB::table('aa_aziende')
            ->where('user_id', $user->id)
            ->where('attiva', true)
            ->select('id', 'nome', 'settore', 'codice_ateco', 'partita_iva', 'dimensione')
            ->get();

        return response()->json(['success' => true, 'data' => $aziende]);
    }

    /**
     * Aggiorna dati azienda (codice ATECO, settore, ecc.)
     */
    public function aggiornaAzienda(Request $request, int $id): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $azienda = DB::table('aa_aziende')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$azienda) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
        }

        $updates = [];
        if ($request->has('codice_ateco')) {
            $updates['codice_ateco'] = $request->codice_ateco;
            // Auto-imposta settore dal codice ATECO
            $settoreLabel = $this->getSettoreLabel($request->codice_ateco);
            if ($settoreLabel) {
                $updates['settore'] = $settoreLabel;
            }
        }
        if ($request->has('nome')) $updates['nome'] = $request->nome;
        if ($request->has('partita_iva')) $updates['partita_iva'] = $request->partita_iva;

        if (!empty($updates)) {
            $updates['updated_at'] = now();
            DB::table('aa_aziende')->where('id', $id)->update($updates);
        }

        $azienda = DB::table('aa_aziende')->where('id', $id)->first();

        return response()->json(['success' => true, 'data' => $azienda]);
    }

    private function getSettoreLabel(?string $codiceAteco): ?string
    {
        if (!$codiceAteco) return null;
        $prefisso = substr($codiceAteco, 0, 2);
        $labels = [
            '01' => 'Agricoltura', '02' => 'Silvicoltura', '03' => 'Pesca',
            '10' => 'Alimentare', '25' => 'Metalmeccanica', '41' => 'Edilizia',
            '42' => 'Ingegneria civile', '43' => 'Costruzioni specializzate',
            '45' => 'Commercio autoveicoli', '46' => 'Commercio ingrosso', '47' => 'Commercio dettaglio',
            '49' => 'Trasporto terrestre', '55' => 'Alloggio', '56' => 'Ristorazione',
            '62' => 'Software e ICT', '69' => 'Studi professionali',
            '86' => 'Servizi sanitari', '96' => 'Servizi alla persona',
        ];
        return $labels[$prefisso] ?? null;
    }

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
                // Stato Patrimoniale
                'patrimonio_netto' => $request->patrimonio_netto,
                'totale_attivo' => $request->totale_attivo,
                'attivo_circolante' => $request->attivo_circolante,
                'debiti_breve_termine' => $request->debiti_breve_termine,
                // Debiti
                'debiti_totali' => $request->debiti_totali,
                'debiti_tributari' => $request->debiti_tributari,
                'oneri_finanziari' => $request->oneri_finanziari,
                'debiti_vs_fornitori' => $request->debiti_fornitori,
                'debiti_banche_breve' => $request->debiti_banche_breve,
                'debiti_banche_lungo' => $request->debiti_banche_lungo,
                // Conto Economico
                'totale_ricavi' => $request->totale_ricavi,
                'costi_materie_prime' => $request->costi_materie_prime,
                'costi_servizi' => $request->costi_servizi,
                'costi_personale' => $request->costi_personale,
                // Crediti
                'crediti_vs_clienti' => $request->crediti_vs_clienti,
                'crediti_scaduti_90gg' => $request->crediti_scaduti_90gg,
                // KPI Settoriali Studio
                'numero_poltrone' => $request->numero_poltrone,
                'ore_disponibili' => $request->ore_disponibili,
                'ore_appuntamenti' => $request->ore_appuntamenti,
                'pazienti_attivi' => $request->pazienti_attivi,
                'preventivi_presentati' => $request->preventivi_presentati,
                'preventivi_accettati' => $request->preventivi_accettati,
                'pazienti_nuovi' => $request->pazienti_nuovi,
                'appuntamenti_no_show' => $request->appuntamenti_no_show,
                'appuntamenti_totali' => $request->appuntamenti_totali,
                'pazienti_recall' => $request->pazienti_recall,
                // Meta
                'note' => $request->note,
                'updated_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Dati salvati con successo'
        ]);
    }

    /**
     * Calcola KPI dal dati economici - Sistema gerarchico CNDCEC
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

        // Recupera soglie settoriali CNDCEC dal codice ATECO
        $settoreCndcec = $this->mapAtecoToSettore($azienda->codice_ateco);
        $soglie = self::SOGLIE_SETTORIALI[$settoreCndcec] ?? self::SOGLIE_SETTORIALI['DEFAULT'];

        // Recupera definizioni KPI
        $kpiDefs = DB::table('aa_kpi_definizioni')->where('attivo', true)->get();

        $risultati = [];
        $alertCritico = false;

        // STEP 1: Verifica Patrimonio Netto (primo controllo gerarchico)
        $pn = $dati->patrimonio_netto ?? 0;
        if ($pn < 0) {
            $alertCritico = true;
        }

        foreach ($kpiDefs as $kpi) {
            $valore = $this->calcolaValore($kpi->codice, $dati, $soglie);
            $stato = $this->determinaStato($valore, $kpi, $soglie);

            // Calcola delta vs mese precedente
            $delta = $this->calcolaDelta($azienda->id, $kpi->codice, $valore, $request->anno, $request->mese);

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
                    'delta_precedente' => $delta,
                    'calcolato_il' => now(),
                ]
            );

            // Genera alert se critico o attenzione
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
                        'messaggio' => $this->getMessaggioAlert($kpi, $valore, $stato),
                        'azione_suggerita' => $this->getAzioneSuggerita($kpi->codice),
                        'letto' => false,
                        'created_at' => now(),
                    ]
                );
            } else {
                // Rimuovi alert se ora è verde
                DB::table('aa_alert')
                    ->where('azienda_id', $azienda->id)
                    ->where('kpi_codice', $kpi->codice)
                    ->where('anno', $request->anno)
                    ->where('mese', $request->mese)
                    ->delete();
            }

            $risultati[] = [
                'codice' => $kpi->codice,
                'nome' => $kpi->nome,
                'valore' => $valore,
                'stato' => $stato,
                'delta' => $delta,
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
        // Try standard Bearer token first
        $token = $request->bearerToken();

        // Fallback: check Authorization header directly (some proxies strip it)
        if (!$token && $request->header('Authorization')) {
            $auth = $request->header('Authorization');
            if (str_starts_with($auth, 'Bearer ')) {
                $token = substr($auth, 7);
            }
        }

        // Fallback: check HTTP_AUTHORIZATION server variable
        if (!$token && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
            if (str_starts_with($auth, 'Bearer ')) {
                $token = substr($auth, 7);
            }
        }

        // Fallback: check REDIRECT_HTTP_AUTHORIZATION (Apache mod_rewrite)
        if (!$token && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            if (str_starts_with($auth, 'Bearer ')) {
                $token = substr($auth, 7);
            }
        }

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

    /**
     * Calcola valore KPI con formule corrette CNDCEC
     */
    private function calcolaValore(string $codice, $dati, array $soglie): ?float
    {
        switch ($codice) {
            // KPI OBBLIGATORI CNDCEC
            case 'PN':
                return $dati->patrimonio_netto ?? null;

            case 'DSCR':
                // DSCR = Cash Flow Operativo / (Debiti Breve + Oneri Finanziari)
                $cashFlow = ($dati->totale_ricavi ?? 0) - ($dati->costi_materie_prime ?? 0)
                    - ($dati->costi_servizi ?? 0) - ($dati->costi_personale ?? 0);
                $debitoServizio = ($dati->debiti_breve_termine ?? 0) + ($dati->oneri_finanziari ?? 0);
                return $debitoServizio > 0 ? round($cashFlow / $debitoServizio, 2) : null;

            case 'CR':
                // Current Ratio = Attivo Circolante / Debiti Breve Termine (in %)
                return ($dati->debiti_breve_termine ?? 0) > 0
                    ? round((($dati->attivo_circolante ?? 0) / $dati->debiti_breve_termine) * 100, 1) : null;

            case 'OF_RIC':
                // Oneri Finanziari / Ricavi (%)
                return ($dati->totale_ricavi ?? 0) > 0
                    ? round((($dati->oneri_finanziari ?? 0) / $dati->totale_ricavi) * 100, 2) : null;

            case 'PN_DEB':
                // Patrimonio Netto / Debiti Totali (%)
                return ($dati->debiti_totali ?? 0) > 0
                    ? round((($dati->patrimonio_netto ?? 0) / $dati->debiti_totali) * 100, 2) : null;

            case 'CF_ATT':
                // Cash Flow / Totale Attivo (%)
                $cashFlow = ($dati->totale_ricavi ?? 0) - ($dati->costi_materie_prime ?? 0)
                    - ($dati->costi_servizi ?? 0) - ($dati->costi_personale ?? 0);
                return ($dati->totale_attivo ?? 0) > 0
                    ? round(($cashFlow / $dati->totale_attivo) * 100, 2) : null;

            case 'DEBFISC_ATT':
                // Debiti Tributari / Totale Attivo (%)
                return ($dati->totale_attivo ?? 0) > 0
                    ? round((($dati->debiti_tributari ?? 0) / $dati->totale_attivo) * 100, 2) : null;

            case 'DEB_SCAD_TOT':
                // Debiti scaduti totali (placeholder - da implementare con gestione scadenze)
                return 0;

            // KPI SETTORIALI
            case 'MARG_LORDO':
                // Margine Lordo % = (Ricavi - Materie - Servizi) / Ricavi * 100
                $margine = ($dati->totale_ricavi ?? 0) - ($dati->costi_materie_prime ?? 0) - ($dati->costi_servizi ?? 0);
                return ($dati->totale_ricavi ?? 0) > 0
                    ? round(($margine / $dati->totale_ricavi) * 100, 1) : null;

            case 'OCC_POLT':
                // Occupazione Poltrone % = Ore Appuntamenti / Ore Disponibili * 100
                return ($dati->ore_disponibili ?? 0) > 0
                    ? round((($dati->ore_appuntamenti ?? 0) / $dati->ore_disponibili) * 100, 1) : null;

            case 'DSO':
                // Days Sales Outstanding = (Crediti Clienti / Ricavi Annualizzati) * 365
                $ricaviAnnui = ($dati->totale_ricavi ?? 0) * 12;
                return $ricaviAnnui > 0
                    ? round((($dati->crediti_vs_clienti ?? 0) / $ricaviAnnui) * 365, 0) : null;

            case 'FATT_POLT':
                // Fatturato per Poltrona = Ricavi / Numero Poltrone
                return ($dati->numero_poltrone ?? 0) > 0
                    ? round(($dati->totale_ricavi ?? 0) / $dati->numero_poltrone, 0) : null;

            case 'CONV_PREV':
                // Conversione Preventivi % = Accettati / Presentati * 100
                return ($dati->preventivi_presentati ?? 0) > 0
                    ? round((($dati->preventivi_accettati ?? 0) / $dati->preventivi_presentati) * 100, 1) : null;

            case 'CRED_SCAD':
                // Crediti Scaduti % = Crediti Scaduti 90gg / Crediti Totali * 100
                return ($dati->crediti_vs_clienti ?? 0) > 0
                    ? round((($dati->crediti_scaduti_90gg ?? 0) / $dati->crediti_vs_clienti) * 100, 1) : null;

            case 'CPERS_FATT':
                // Costo Personale / Fatturato %
                return ($dati->totale_ricavi ?? 0) > 0
                    ? round((($dati->costi_personale ?? 0) / $dati->totale_ricavi) * 100, 1) : null;

            // KPI OPERATIVI
            case 'NEW_PAZ':
                return $dati->pazienti_nuovi ?? null;

            case 'NO_SHOW':
                // No Show % = No Show / Appuntamenti Totali * 100
                return ($dati->appuntamenti_totali ?? 0) > 0
                    ? round((($dati->appuntamenti_no_show ?? 0) / $dati->appuntamenti_totali) * 100, 1) : null;

            case 'RECALL':
                // Tasso Recall % = Pazienti Recall / Pazienti Attivi * 100
                return ($dati->pazienti_attivi ?? 0) > 0
                    ? round((($dati->pazienti_recall ?? 0) / $dati->pazienti_attivi) * 100, 1) : null;

            case 'TICK_MED':
                // Ticket Medio = Ricavi / Pazienti Attivi
                return ($dati->pazienti_attivi ?? 0) > 0
                    ? round(($dati->totale_ricavi ?? 0) / $dati->pazienti_attivi, 0) : null;

            case 'EBITDA_MARG':
                // EBITDA Margin = EBITDA / Ricavi * 100
                $ebitda = ($dati->totale_ricavi ?? 0) - ($dati->costi_materie_prime ?? 0)
                    - ($dati->costi_servizi ?? 0) - ($dati->costi_personale ?? 0);
                return ($dati->totale_ricavi ?? 0) > 0
                    ? round(($ebitda / $dati->totale_ricavi) * 100, 1) : null;

            case 'ROE':
                // ROE = Risultato Netto / Patrimonio Netto * 100 (annualizzato)
                $risultato = ($dati->totale_ricavi ?? 0) - ($dati->costi_materie_prime ?? 0)
                    - ($dati->costi_servizi ?? 0) - ($dati->costi_personale ?? 0) - ($dati->oneri_finanziari ?? 0);
                return ($dati->patrimonio_netto ?? 0) > 0
                    ? round(($risultato * 12 / $dati->patrimonio_netto) * 100, 1) : null;

            case 'NPS':
                // NPS - placeholder (da implementare con survey)
                return null;

            default:
                return null;
        }
    }

    /**
     * Determina stato KPI con soglie settoriali CNDCEC
     */
    private function determinaStato(?float $valore, $kpi, array $soglie): string
    {
        if ($valore === null) return 'nd';

        // Usa soglie settoriali per KPI CNDCEC obbligatori
        $sogliaVerde = $kpi->soglia_verde;
        $sogliaGialla = $kpi->soglia_gialla;

        // Override con soglie CNDCEC per KPI obbligatori
        switch ($kpi->codice) {
            case 'OF_RIC':
                $sogliaVerde = $soglie['of_ric'] * 0.7; // 70% della soglia alert
                $sogliaGialla = $soglie['of_ric'];
                break;
            case 'PN_DEB':
                $sogliaVerde = $soglie['pn_deb'] * 1.5;
                $sogliaGialla = $soglie['pn_deb'];
                break;
            case 'CF_ATT':
                $sogliaVerde = $soglie['cf_att'] * 2;
                $sogliaGialla = $soglie['cf_att'];
                break;
            case 'CR':
                $sogliaVerde = $soglie['cr'] * 1.2;
                $sogliaGialla = $soglie['cr'];
                break;
            case 'DEBFISC_ATT':
                $sogliaVerde = $soglie['debfisc_att'] * 0.5;
                $sogliaGialla = $soglie['debfisc_att'];
                break;
        }

        if ($kpi->direzione === 'maggiore') {
            if ($valore >= $sogliaVerde) return 'verde';
            if ($valore >= $sogliaGialla) return 'giallo';
            return 'rosso';
        } else {
            if ($valore <= $sogliaVerde) return 'verde';
            if ($valore <= $sogliaGialla) return 'giallo';
            return 'rosso';
        }
    }

    /**
     * Calcola delta vs mese precedente
     */
    private function calcolaDelta(int $aziendaId, string $codice, ?float $valoreAttuale, int $anno, int $mese): ?float
    {
        if ($valoreAttuale === null) return null;

        // Calcola mese precedente
        $mesePrecedente = $mese - 1;
        $annoPrecedente = $anno;
        if ($mesePrecedente < 1) {
            $mesePrecedente = 12;
            $annoPrecedente--;
        }

        $valorePrecedente = DB::table('aa_kpi_valori')
            ->where('azienda_id', $aziendaId)
            ->where('kpi_codice', $codice)
            ->where('anno', $annoPrecedente)
            ->where('mese', $mesePrecedente)
            ->value('valore');

        if ($valorePrecedente === null || $valorePrecedente == 0) return null;

        return round((($valoreAttuale - $valorePrecedente) / abs($valorePrecedente)) * 100, 1);
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

    private function getMessaggioAlert($kpi, ?float $valore, string $stato): string
    {
        $valoreStr = $valore !== null ? number_format($valore, 2, ',', '.') : 'N/D';
        $livello = $stato === 'rosso' ? 'CRITICO' : 'ATTENZIONE';
        return "[{$livello}] {$kpi->nome}: {$valoreStr} {$kpi->unita_misura}";
    }

    private function getAzioneSuggerita(string $codice): string
    {
        $azioni = [
            'PN' => 'Valutare ricapitalizzazione o copertura perdite',
            'DSCR' => 'Accelerare incassi, rinegoziare debiti, tagliare costi non essenziali',
            'CR' => 'Migliorare gestione liquidita, convertire attivi in liquidita',
            'OF_RIC' => 'Rinegoziare condizioni finanziamenti, ridurre indebitamento',
            'PN_DEB' => 'Rafforzare patrimonio, ridurre debiti',
            'CF_ATT' => 'Migliorare margini operativi, ottimizzare capitale circolante',
            'DEBFISC_ATT' => 'Regolarizzare posizione fiscale, pianificare rateizzazioni',
            'DSO' => 'Sollecitare crediti, rivedere condizioni pagamento clienti',
            'MARG_LORDO' => 'Rivedere listino prezzi, ottimizzare costi diretti',
            'OCC_POLT' => 'Ottimizzare agenda, aumentare marketing acquisizione',
            'CONV_PREV' => 'Migliorare presentazione preventivi, formazione staff',
            'CRED_SCAD' => 'Intensificare solleciti, valutare azioni legali',
            'CPERS_FATT' => 'Ottimizzare organico, aumentare produttivita',
            'NO_SHOW' => 'Implementare sistema reminder, politica no-show',
            'RECALL' => 'Attivare campagne recall, migliorare follow-up',
        ];
        return $azioni[$codice] ?? 'Monitorare e analizzare le cause';
    }
}
