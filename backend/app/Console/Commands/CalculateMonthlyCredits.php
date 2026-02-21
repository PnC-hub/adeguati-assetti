<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalculateMonthlyCredits extends Command
{
    protected $signature = 'crediti:calcola {--anno= : Anno (default: mese precedente)} {--mese= : Mese (default: mese precedente)} {--dry-run : Solo simulazione}';
    protected $description = 'Calcola crediti revenue share per i commercialisti (20% su mesi completi)';

    private const IMPORTO_ABBONAMENTO = 49.00;
    private const PERCENTUALE_SHARE = 0.20;
    private const IMPORTO_CREDITO = 9.80; // 49 * 0.20

    public function handle(): int
    {
        // Default: mese precedente
        $now = Carbon::now();
        $anno = $this->option('anno') ?? $now->copy()->subMonth()->year;
        $mese = $this->option('mese') ?? $now->copy()->subMonth()->month;
        $dryRun = $this->option('dry-run');

        $primoGiorno = Carbon::create($anno, $mese, 1)->startOfDay();
        $ultimoGiorno = Carbon::create($anno, $mese, 1)->endOfMonth()->endOfDay();

        $this->info("Calcolo crediti per {$mese}/{$anno}");
        $this->info("Periodo: {$primoGiorno->toDateString()} - {$ultimoGiorno->toDateString()}");

        if ($dryRun) {
            $this->warn('DRY RUN - nessuna modifica al database');
        }

        // Get all active client-commercialista links
        $links = DB::table('aa_client_commercialista')
            ->where('stato', 'active')
            ->where('linked_at', '<=', $ultimoGiorno)
            ->get();

        $this->info("Link attivi trovati: {$links->count()}");

        $creditiValidi = 0;
        $creditiNonValidi = 0;

        foreach ($links as $link) {
            $client = DB::table('aa_users')->find($link->client_user_id);

            if (!$client) {
                $this->warn("  Utente {$link->client_user_id} non trovato, skip");
                continue;
            }

            // Check subscription was active on first day of month
            $activeOnFirst = false;
            if ($client->subscription_started_at) {
                $subStart = Carbon::parse($client->subscription_started_at);
                $subEnd = $client->subscription_ended_at ? Carbon::parse($client->subscription_ended_at) : null;

                $activeOnFirst = $subStart->lte($primoGiorno)
                    && ($subEnd === null || $subEnd->gte($primoGiorno));
            }

            // Check subscription was active on last day of month
            $activeOnLast = false;
            if ($client->subscription_started_at) {
                $subStart = Carbon::parse($client->subscription_started_at);
                $subEnd = $client->subscription_ended_at ? Carbon::parse($client->subscription_ended_at) : null;

                $activeOnLast = $subStart->lte($ultimoGiorno)
                    && ($subEnd === null || $subEnd->gte($ultimoGiorno));
            }

            // Check link was active for entire month
            $linkActiveFullMonth = Carbon::parse($link->linked_at)->lte($primoGiorno)
                && ($link->terminated_at === null || Carbon::parse($link->terminated_at)->gte($ultimoGiorno));

            $creditoValido = $activeOnFirst && $activeOnLast && $linkActiveFullMonth;
            $importo = $creditoValido ? self::IMPORTO_CREDITO : 0.00;

            $this->line(sprintf(
                "  Cliente: %s %s (id:%d) | Sub 1°:%s Ultimo:%s | Link intero:%s | Credito: %s (€%.2f)",
                $client->nome, $client->cognome, $client->id,
                $activeOnFirst ? 'SI' : 'NO',
                $activeOnLast ? 'SI' : 'NO',
                $linkActiveFullMonth ? 'SI' : 'NO',
                $creditoValido ? 'VALIDO' : 'NON VALIDO',
                $importo
            ));

            if ($creditoValido) {
                $creditiValidi++;
            } else {
                $creditiNonValidi++;
            }

            if (!$dryRun) {
                DB::table('aa_crediti_commercialista')->updateOrInsert(
                    [
                        'commercialista_user_id' => $link->commercialista_user_id,
                        'client_user_id' => $link->client_user_id,
                        'anno' => $anno,
                        'mese' => $mese,
                    ],
                    [
                        'azienda_id' => $link->azienda_id,
                        'importo_credito' => $importo,
                        'credito_valido' => $creditoValido,
                        'stato' => 'calcolato',
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->info("--- Riepilogo ---");
        $this->info("Crediti validi: {$creditiValidi} (€" . number_format($creditiValidi * self::IMPORTO_CREDITO, 2) . ")");
        $this->info("Crediti non validi: {$creditiNonValidi}");

        return Command::SUCCESS;
    }
}
