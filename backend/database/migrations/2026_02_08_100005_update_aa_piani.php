<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cancella piani esistenti
        DB::table('aa_piani')->truncate();

        // Inserisci nuovi piani
        DB::table('aa_piani')->insert([
            [
                'codice' => 'free',
                'nome' => 'Free Forever',
                'prezzo_mensile' => 0,
                'prezzo_annuale' => 0,
                'max_aziende' => 1,
                'features' => json_encode(['7_kpi', 'calcolo_manuale', '1_mese_storico']),
                'target' => 'imprenditore',
                'attivo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codice' => 'pro',
                'nome' => 'Pro',
                'prezzo_mensile' => 29,
                'prezzo_annuale' => 290,
                'max_aziende' => 1,
                'features' => json_encode(['7_kpi', 'kpi_ateco', 'alert_email', 'export_pdf', '24_mesi_storico', 'simulazioni']),
                'target' => 'imprenditore',
                'attivo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codice' => 'business',
                'nome' => 'Business',
                'prezzo_mensile' => 79,
                'prezzo_annuale' => 790,
                'max_aziende' => 20,
                'features' => json_encode(['7_kpi', 'kpi_ateco', 'alert_email', 'export_pdf', '24_mesi_storico', 'simulazioni', 'dashboard_aggregata', 'alert_tutti', 'export_batch', 'invita_clienti', 'report_commercialista']),
                'target' => 'consulente',
                'attivo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codice' => 'enterprise',
                'nome' => 'Enterprise',
                'prezzo_mensile' => 199,
                'prezzo_annuale' => 1990,
                'max_aziende' => -1,
                'features' => json_encode(['7_kpi', 'kpi_ateco', 'alert_email', 'export_pdf', '24_mesi_storico', 'simulazioni', 'dashboard_aggregata', 'alert_tutti', 'export_batch', 'invita_clienti', 'report_commercialista', 'white_label', 'api', 'sub_account', 'supporto_prioritario', 'formazione']),
                'target' => 'consulente',
                'attivo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('aa_piani')->truncate();
    }
};
