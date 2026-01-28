<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdeguatiAssettiService;
use App\Models\AdeguatiAssettiKpi;
use App\Models\AdeguatiAssettiValore;
use App\Models\AdeguatiAssettiDatiEconomici;
use App\Models\AdeguatiAssettiAlert;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Exception;

class AdeguatiAssettiController extends Controller
{
    protected AdeguatiAssettiService $service;

    public function __construct(AdeguatiAssettiService $service)
    {
        $this->service = $service;
    }

    /**
     * Dashboard principale
     * GET /api/v1/adeguati-assetti/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        $anno = $request->get('anno', now()->year);
        $mese = $request->get('mese', now()->month);

        try {
            $dashboard = $this->service->getDashboard($centroId, $anno, $mese);
            return response()->json(['success' => true, 'data' => $dashboard]);
        } catch (Exception $e) {
            // Log error and return mock data
            \Log::warning('AdeguatiAssetti dashboard error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'data' => $this->getMockDashboard($anno, $mese),
                'mock' => true,
                'message' => 'Dati di esempio - configurare database per dati reali'
            ]);
        }
    }

    /**
     * Restituisce dati mock per la dashboard
     */
    private function getMockDashboard(int $anno, int $mese): array
    {
        return [
            'periodo' => [
                'anno' => $anno,
                'mese' => $mese,
                'label' => Carbon::create($anno, $mese)->locale('it')->translatedFormat('F Y'),
            ],
            'score' => 82,
            'stato_generale' => 'buono',
            'kpi_obbligatori' => [
                ['codice' => 'PN', 'nome' => 'Patrimonio Netto', 'valore' => 125000, 'stato' => 'verde', 'unita_misura' => 'euro', 'delta_precedente' => 5000],
                ['codice' => 'DSCR', 'nome' => 'DSCR', 'valore' => 1.45, 'stato' => 'verde', 'unita_misura' => 'ratio', 'delta_precedente' => 0.12],
                ['codice' => 'CURRENT_RATIO', 'nome' => 'Current Ratio', 'valore' => 1.82, 'stato' => 'verde', 'unita_misura' => 'ratio', 'delta_precedente' => -0.08],
                ['codice' => 'OF_RICAVI', 'nome' => 'Oneri Fin./Ricavi', 'valore' => 0.018, 'stato' => 'verde', 'unita_misura' => '%', 'delta_precedente' => 0],
                ['codice' => 'PN_DEBITI', 'nome' => 'PN/Debiti', 'valore' => 0.15, 'stato' => 'giallo', 'unita_misura' => 'ratio', 'delta_precedente' => -0.02],
                ['codice' => 'CF_ATTIVO', 'nome' => 'CF/Attivo', 'valore' => 0.042, 'stato' => 'verde', 'unita_misura' => '%', 'delta_precedente' => 0.005],
                ['codice' => 'DEBITI_FISC_ATTIVO', 'nome' => 'Deb.Fiscali/Attivo', 'valore' => 0.021, 'stato' => 'verde', 'unita_misura' => '%', 'delta_precedente' => -0.003],
            ],
            'kpi_settoriali' => [
                ['codice' => 'MARGINE_LORDO', 'nome' => 'Margine Lordo', 'valore' => 0.68, 'stato' => 'verde', 'unita_misura' => '%', 'delta_precedente' => 0.02],
                ['codice' => 'OCCUPAZ_POLTRONE', 'nome' => 'Occupazione Poltrone', 'valore' => 0.62, 'stato' => 'giallo', 'unita_misura' => '%', 'delta_precedente' => -0.03],
                ['codice' => 'DSO', 'nome' => 'Giorni Incasso', 'valore' => 72, 'stato' => 'rosso', 'unita_misura' => 'giorni', 'delta_precedente' => 8],
                ['codice' => 'FATT_POLTRONA', 'nome' => 'Fatt./Poltrona', 'valore' => 16200, 'stato' => 'verde', 'unita_misura' => 'euro', 'delta_precedente' => 1200],
                ['codice' => 'CONV_PREVENTIVI', 'nome' => 'Conv. Preventivi', 'valore' => 0.58, 'stato' => 'giallo', 'unita_misura' => '%', 'delta_precedente' => -0.04],
                ['codice' => 'CREDITI_SCADUTI', 'nome' => 'Crediti Scaduti', 'valore' => 0.18, 'stato' => 'giallo', 'unita_misura' => '%', 'delta_precedente' => 0.03],
                ['codice' => 'COSTO_PERS_FATT', 'nome' => 'Costo Pers./Fatt.', 'valore' => 0.32, 'stato' => 'verde', 'unita_misura' => '%', 'delta_precedente' => -0.01],
            ],
            'alert' => [
                ['id' => 1, 'livello' => 'critical', 'messaggio' => 'DSO critico: 72 giorni (soglia: 30)', 'azione_suggerita' => 'Implementare solleciti automatici, rivedere politiche di pagamento'],
                ['id' => 2, 'livello' => 'warning', 'messaggio' => 'Occupazione poltrone sotto target: 62%', 'azione_suggerita' => 'Lanciare campagna richiami, ottimizzare agenda'],
            ],
            'alert_count' => 2,
        ];
    }

    /**
     * Dashboard per mese specifico
     * GET /api/v1/adeguati-assetti/dashboard/{anno}/{mese}
     */
    public function dashboardPeriodo(int $anno, int $mese): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        try {
            $dashboard = $this->service->getDashboard($centroId, $anno, $mese);
            return response()->json(['success' => true, 'data' => $dashboard]);
        } catch (Exception $e) {
            \Log::warning('AdeguatiAssetti dashboard error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'data' => $this->getMockDashboard($anno, $mese),
                'mock' => true
            ]);
        }
    }

    /**
     * Lista KPI configurati
     * GET /api/v1/adeguati-assetti/kpi
     */
    public function listaKpi(): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        try {
            $kpi = AdeguatiAssettiKpi::perCentro($centroId)
                ->attivi()
                ->orderBy('ordine')
                ->get();
            return response()->json(['success' => true, 'kpi' => $kpi]);
        } catch (Exception $e) {
            return response()->json(['success' => true, 'kpi' => [], 'mock' => true]);
        }
    }

    /**
     * Dettaglio KPI con storico
     * GET /api/v1/adeguati-assetti/kpi/{codice}
     */
    public function dettaglioKpi(string $codice): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        try {
            $storico = $this->service->getStoricoKPI($centroId, $codice, 12);

            // Se non ci sono dati (KPI non trovato o senza storico), restituisci mock
            if (empty($storico)) {
                \Log::info("AdeguatiAssetti: KPI {$codice} non trovato o senza dati per centro {$centroId}, restituendo mock");
                return response()->json([
                    'success' => true,
                    'data' => $this->getMockKpiStorico($codice),
                    'mock' => true,
                    'message' => 'KPI non configurato - dati di esempio'
                ]);
            }

            return response()->json(['success' => true, 'data' => $storico]);
        } catch (Exception $e) {
            \Log::warning("AdeguatiAssetti: Errore dettaglio KPI {$codice}: " . $e->getMessage());
            return response()->json([
                'success' => true,
                'data' => $this->getMockKpiStorico($codice),
                'mock' => true
            ]);
        }
    }

    /**
     * Mock storico KPI
     */
    private function getMockKpiStorico(string $codice): array
    {
        $kpiMeta = [
            'DSO' => ['nome' => 'Giorni Incasso (DSO)', 'unita' => 'giorni', 'desc' => 'Tempo medio di incasso dai pazienti'],
            'DSCR' => ['nome' => 'DSCR', 'unita' => 'ratio', 'desc' => 'Debt Service Coverage Ratio'],
            'MARGINE_LORDO' => ['nome' => 'Margine Lordo', 'unita' => '%', 'desc' => 'Margine di contribuzione sui ricavi'],
        ];

        $meta = $kpiMeta[$codice] ?? ['nome' => $codice, 'unita' => 'valore', 'desc' => ''];

        return [
            'kpi' => [
                'codice' => $codice,
                'nome' => $meta['nome'],
                'descrizione' => $meta['desc'],
                'unita_misura' => $meta['unita'],
                'soglia_verde' => 1.2,
                'soglia_gialla' => 1.0,
                'soglia_rossa' => 0.8,
            ],
            'storico' => [
                ['periodo' => '2025-01', 'label' => 'Gen 2025', 'valore' => 1.1, 'stato' => 'giallo'],
                ['periodo' => '2025-02', 'label' => 'Feb 2025', 'valore' => 1.15, 'stato' => 'giallo'],
                ['periodo' => '2025-03', 'label' => 'Mar 2025', 'valore' => 1.2, 'stato' => 'verde'],
                ['periodo' => '2025-04', 'label' => 'Apr 2025', 'valore' => 1.25, 'stato' => 'verde'],
                ['periodo' => '2025-05', 'label' => 'Mag 2025', 'valore' => 1.18, 'stato' => 'giallo'],
                ['periodo' => '2025-06', 'label' => 'Giu 2025', 'valore' => 1.22, 'stato' => 'verde'],
            ],
        ];
    }

    /**
     * Aggiorna soglie KPI
     * PUT /api/v1/adeguati-assetti/kpi/{id}
     */
    public function aggiornaSoglieKpi(Request $request, int $id): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        try {
            $kpi = AdeguatiAssettiKpi::where('id', $id)
                ->where('id_centro', $centroId)
                ->first();

            if (!$kpi) {
                \Log::warning("AdeguatiAssetti: Tentativo di aggiornare KPI {$id} non trovato per centro {$centroId}");
                return response()->json([
                    'success' => false,
                    'error' => 'KPI non trovato o non accessibile'
                ], 404);
            }

            $validated = $request->validate([
                'soglia_verde' => 'nullable|numeric',
                'soglia_gialla' => 'nullable|numeric',
                'soglia_rossa' => 'nullable|numeric',
                'attivo' => 'nullable|boolean',
            ]);

            $kpi->update($validated);

            return response()->json(['success' => true, 'kpi' => $kpi]);
        } catch (Exception $e) {
            \Log::error("AdeguatiAssetti: Errore aggiornamento KPI {$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Dati economici di un mese
     * GET /api/v1/adeguati-assetti/dati-economici/{anno}/{mese}
     */
    public function getDatiEconomici(int $anno, int $mese): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        try {
            $dati = AdeguatiAssettiDatiEconomici::perCentro($centroId)
                ->periodo($anno, $mese)
                ->first();

            return response()->json([
                'success' => true,
                'dati' => $dati,
                'periodo' => [
                    'anno' => $anno,
                    'mese' => $mese,
                    'label' => Carbon::create($anno, $mese)->locale('it')->translatedFormat('F Y'),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => true,
                'dati' => null,
                'periodo' => ['anno' => $anno, 'mese' => $mese],
                'mock' => true
            ]);
        }
    }

    /**
     * Salva dati economici
     * POST /api/v1/adeguati-assetti/dati-economici
     */
    public function salvaDatiEconomici(Request $request): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        $validated = $request->validate([
            'anno' => 'required|integer|min:2020|max:2100',
            'mese' => 'required|integer|min:1|max:12',
            'patrimonio_netto' => 'nullable|numeric',
            'totale_attivo' => 'nullable|numeric',
            'attivita_correnti' => 'nullable|numeric',
            'passivita_correnti' => 'nullable|numeric',
            'saldo_banca' => 'nullable|numeric',
            'saldo_cassa' => 'nullable|numeric',
            'debiti_fornitori' => 'nullable|numeric',
            'debiti_inps' => 'nullable|numeric',
            'debiti_erario' => 'nullable|numeric',
            'debiti_finanziari_breve' => 'nullable|numeric',
            'debiti_finanziari_lungo' => 'nullable|numeric',
            'rate_finanziamenti_mensili' => 'nullable|numeric',
            'costi_fissi_mensili' => 'nullable|numeric',
            'costo_personale_mensile' => 'nullable|numeric',
            'costi_variabili_mensili' => 'nullable|numeric',
            'interessi_passivi_mensili' => 'nullable|numeric',
            'numero_dipendenti' => 'nullable|integer',
            'numero_poltrone' => 'nullable|integer',
            'ore_disponibili_mese' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        try {
            $dati = AdeguatiAssettiDatiEconomici::updateOrCreate(
                [
                    'id_centro' => $centroId,
                    'anno' => $validated['anno'],
                    'mese' => $validated['mese'],
                ],
                collect($validated)->except(['anno', 'mese'])->toArray()
            );

            return response()->json(['success' => true, 'dati' => $dati]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Calcola/ricalcola KPI
     * POST /api/v1/adeguati-assetti/calcola
     */
    public function calcolaKpi(Request $request): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        $anno = $request->get('anno', now()->year);
        $mese = $request->get('mese', now()->month);

        try {
            $risultati = $this->service->calcolaKPI($centroId, $anno, $mese);
            $score = $this->service->calcolaScoreComplessivo($centroId, $anno, $mese);

            return response()->json([
                'success' => true,
                'kpi' => $risultati,
                'score' => $score,
                'periodo' => [
                    'anno' => $anno,
                    'mese' => $mese,
                ],
            ]);
        } catch (Exception $e) {
            \Log::warning('AdeguatiAssetti calcola error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'message' => 'Calcolo non disponibile - usando dati di esempio',
                'mock' => true
            ]);
        }
    }

    /**
     * Lista alert
     * GET /api/v1/adeguati-assetti/alert
     */
    public function listaAlert(Request $request): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        try {
            $soloNonLetti = $request->get('non_letti', true);

            $query = AdeguatiAssettiAlert::perCentro($centroId)
                ->with('kpi')
                ->orderBy('created_at', 'desc');

            if ($soloNonLetti) {
                $query->nonLetti();
            }

            $alert = $query->limit(50)->get();

            return response()->json([
                'success' => true,
                'alert' => $alert->map(function($a) {
                    return [
                        'id' => $a->id,
                        'livello' => $a->livello,
                        'messaggio' => $a->messaggio,
                        'kpi_codice' => $a->kpi ? $a->kpi->codice : null,
                        'kpi_nome' => $a->kpi ? $a->kpi->nome : null,
                        'valore_attuale' => $a->valore_attuale,
                        'soglia_violata' => $a->soglia_violata,
                        'azione_suggerita' => $a->azione_suggerita,
                        'letto' => $a->letto,
                        'periodo' => $a->anno . '-' . str_pad($a->mese, 2, '0', STR_PAD_LEFT),
                        'created_at' => $a->created_at,
                    ];
                }),
                'count' => $alert->count(),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => true, 'alert' => [], 'count' => 0, 'mock' => true]);
        }
    }

    /**
     * Segna alert come letto
     * PUT /api/v1/adeguati-assetti/alert/{id}/letto
     */
    public function segnaAlertLetto(int $id): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        try {
            $alert = AdeguatiAssettiAlert::where('id', $id)
                ->where('id_centro', $centroId)
                ->first();

            if (!$alert) {
                \Log::warning("AdeguatiAssetti: Tentativo di segnare come letto alert {$id} non trovato per centro {$centroId}");
                return response()->json([
                    'success' => false,
                    'error' => 'Alert non trovato o non accessibile'
                ], 404);
            }

            $alert->segnaLetto(0);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            \Log::error("AdeguatiAssetti: Errore segnalazione alert letto {$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Trend storico per grafico
     * GET /api/v1/adeguati-assetti/trend/{codice}
     */
    public function trendKpi(string $codice, Request $request): JsonResponse
    {
        $tenant = app('tenant');
        $centroId = $tenant->id_centro ?? 4;

        $mesi = $request->get('mesi', 12);

        try {
            $storico = $this->service->getStoricoKPI($centroId, $codice, $mesi);

            // Se non ci sono dati (KPI non trovato o senza storico), restituisci mock
            if (empty($storico)) {
                \Log::info("AdeguatiAssetti: Trend KPI {$codice} non trovato o senza dati per centro {$centroId}, restituendo mock");
                $mockStorico = $this->getMockKpiStorico($codice);
                return response()->json([
                    'success' => true,
                    'kpi' => $mockStorico['kpi'],
                    'chart' => [
                        'labels' => array_column($mockStorico['storico'], 'label'),
                        'datasets' => [[
                            'label' => $mockStorico['kpi']['nome'],
                            'data' => array_column($mockStorico['storico'], 'valore'),
                            'borderColor' => '#0891b2',
                            'tension' => 0.3,
                        ]],
                    ],
                    'soglie' => [
                        'verde' => $mockStorico['kpi']['soglia_verde'],
                        'gialla' => $mockStorico['kpi']['soglia_gialla'],
                        'rossa' => $mockStorico['kpi']['soglia_rossa'],
                    ],
                    'mock' => true,
                    'message' => 'KPI non configurato - dati di esempio'
                ]);
            }

            // Formatta per Chart.js
            $labels = $storico['storico']->pluck('label')->toArray();
            $valori = $storico['storico']->pluck('valore')->toArray();

            return response()->json([
                'success' => true,
                'kpi' => $storico['kpi'],
                'chart' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => $storico['kpi']['nome'],
                            'data' => $valori,
                            'borderColor' => '#0891b2',
                            'tension' => 0.3,
                        ],
                    ],
                ],
                'soglie' => [
                    'verde' => $storico['kpi']['soglia_verde'],
                    'gialla' => $storico['kpi']['soglia_gialla'],
                    'rossa' => $storico['kpi']['soglia_rossa'],
                ],
            ]);
        } catch (Exception $e) {
            \Log::warning("AdeguatiAssetti: Errore trend KPI {$codice}: " . $e->getMessage());
            $mockStorico = $this->getMockKpiStorico($codice);
            return response()->json([
                'success' => true,
                'kpi' => $mockStorico['kpi'],
                'chart' => [
                    'labels' => array_column($mockStorico['storico'], 'label'),
                    'datasets' => [[
                        'label' => $mockStorico['kpi']['nome'],
                        'data' => array_column($mockStorico['storico'], 'valore'),
                        'borderColor' => '#0891b2',
                        'tension' => 0.3,
                    ]],
                ],
                'soglie' => [
                    'verde' => $mockStorico['kpi']['soglia_verde'],
                    'gialla' => $mockStorico['kpi']['soglia_gialla'],
                    'rossa' => $mockStorico['kpi']['soglia_rossa'],
                ],
                'mock' => true
            ]);
        }
    }
}
