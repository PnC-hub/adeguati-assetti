<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Business Model V2:
     * - Cliente (imprenditore) paga €49/mese
     * - Commercialista accede gratis, guadagna 20% revenue share
     * - Inviti bidirezionali (client↔commercialista)
     * - Crediti calcolati solo su mesi completi (1°-ultimo giorno)
     */
    public function up(): void
    {
        // 1. Tabella link client-commercialista
        if (!Schema::hasTable('aa_client_commercialista')) {
            Schema::create('aa_client_commercialista', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_user_id');
                $table->unsignedBigInteger('commercialista_user_id');
                $table->unsignedBigInteger('azienda_id');
                $table->enum('stato', ['pending', 'active', 'terminated'])->default('pending');
                $table->enum('invited_by', ['client', 'commercialista']);
                $table->timestamp('linked_at')->nullable();
                $table->timestamp('terminated_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                $table->index('client_user_id');
                $table->index('commercialista_user_id');
                $table->index('azienda_id');
                $table->unique(['client_user_id', 'commercialista_user_id', 'azienda_id'], 'unique_client_comm_azienda');
            });
        }

        // 2. Tabella inviti bidirezionali
        if (!Schema::hasTable('aa_inviti_bidirezionali')) {
            Schema::create('aa_inviti_bidirezionali', function (Blueprint $table) {
                $table->id();
                $table->enum('tipo', ['client_to_commercialista', 'commercialista_to_client']);
                $table->unsignedBigInteger('sender_user_id');
                $table->string('recipient_email', 255);
                $table->unsignedBigInteger('recipient_user_id')->nullable();
                $table->unsignedBigInteger('azienda_id')->nullable();
                $table->unsignedBigInteger('studio_id')->nullable();
                $table->string('token', 64)->unique();
                $table->enum('stato', ['pending', 'accepted', 'revoked', 'expired'])->default('pending');
                $table->timestamp('scade_at');
                $table->timestamp('accepted_at')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('token');
                $table->index('recipient_email');
                $table->index('sender_user_id');
            });
        }

        // 3. Tabella crediti commercialista (revenue share)
        if (!Schema::hasTable('aa_crediti_commercialista')) {
            Schema::create('aa_crediti_commercialista', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('commercialista_user_id');
                $table->unsignedBigInteger('client_user_id');
                $table->unsignedBigInteger('azienda_id');
                $table->integer('anno');
                $table->integer('mese');
                $table->decimal('importo_credito', 10, 2);
                $table->boolean('credito_valido')->default(false);
                $table->enum('stato', ['calcolato', 'confermato', 'pagato'])->default('calcolato');
                $table->timestamp('pagato_at')->nullable();
                $table->text('note')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                $table->unique(['commercialista_user_id', 'client_user_id', 'anno', 'mese'], 'unique_credito_mese');
                $table->index(['commercialista_user_id', 'anno', 'mese'], 'idx_comm_mese');
                $table->index('stato');
            });
        }

        // 4. Aggiungi colonne a aa_users per tracking subscription
        if (!Schema::hasColumn('aa_users', 'subscription_started_at')) {
            Schema::table('aa_users', function (Blueprint $table) {
                $table->timestamp('subscription_started_at')->nullable()->after('piano_scade_il');
                $table->timestamp('subscription_ended_at')->nullable()->after('subscription_started_at');
            });
        }

        // 5. Aggiorna piani: disattiva vecchi, inserisci nuovi
        // Aggiungi colonna 'attivo' e 'target' se non esistono
        if (!Schema::hasColumn('aa_piani', 'attivo')) {
            Schema::table('aa_piani', function (Blueprint $table) {
                $table->boolean('attivo')->default(true)->after('features');
                $table->string('target', 50)->nullable()->after('attivo');
            });
        }

        // Disattiva piani vecchi (mantienili per subscriber esistenti)
        DB::table('aa_piani')->whereIn('codice', ['pro', 'business', 'enterprise'])->update(['attivo' => false]);

        // Inserisci nuovo piano impresa49
        if (!DB::table('aa_piani')->where('codice', 'impresa49')->exists()) {
            DB::table('aa_piani')->insert([
                'codice' => 'impresa49',
                'nome' => 'Impresa',
                'prezzo_mensile' => 49,
                'prezzo_annuale' => 490,
                'max_aziende' => 1,
                'features' => json_encode([
                    '7_kpi' => true,
                    'kpi_ateco' => true,
                    'alert_email' => true,
                    'export_pdf' => true,
                    '24_mesi_storico' => true,
                    'simulazioni' => true,
                    'supporto_prioritario' => true,
                ]),
                'attivo' => true,
                'target' => 'imprenditore',
                'created_at' => now(),
            ]);
        }

        // Inserisci piano commercialista gratuito
        if (!DB::table('aa_piani')->where('codice', 'commercialista_free')->exists()) {
            DB::table('aa_piani')->insert([
                'codice' => 'commercialista_free',
                'nome' => 'Commercialista',
                'prezzo_mensile' => 0,
                'prezzo_annuale' => 0,
                'max_aziende' => -1, // illimitato (vede i clienti linkati)
                'features' => json_encode([
                    'dashboard_aggregata' => true,
                    'alert_tutti' => true,
                    'report_commercialista' => true,
                    'invita_clienti' => true,
                    'vista_clienti' => true,
                    'export_pdf' => true,
                ]),
                'attivo' => true,
                'target' => 'consulente',
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aa_crediti_commercialista');
        Schema::dropIfExists('aa_inviti_bidirezionali');
        Schema::dropIfExists('aa_client_commercialista');

        if (Schema::hasColumn('aa_users', 'subscription_started_at')) {
            Schema::table('aa_users', function (Blueprint $table) {
                $table->dropColumn(['subscription_started_at', 'subscription_ended_at']);
            });
        }

        if (Schema::hasColumn('aa_piani', 'attivo')) {
            Schema::table('aa_piani', function (Blueprint $table) {
                $table->dropColumn(['attivo', 'target']);
            });
        }

        DB::table('aa_piani')->whereIn('codice', ['impresa49', 'commercialista_free'])->delete();
        DB::table('aa_piani')->whereIn('codice', ['pro', 'business', 'enterprise'])->update(['attivo' => true]);
    }
};
