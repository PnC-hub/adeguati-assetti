<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aa_studi', function (Blueprint $table) {
            $table->string('api_key', 64)->nullable()->after('white_label_attivo');
            $table->string('codice_fiscale', 20)->nullable()->after('p_iva');
            $table->string('sito_web', 255)->nullable()->after('email');
            $table->boolean('notifica_kpi_critico')->default(true)->after('sito_web');
            $table->boolean('notifica_report_settimanale')->default(true)->after('notifica_kpi_critico');
            $table->boolean('notifica_invito_accettato')->default(true)->after('notifica_report_settimanale');
            $table->boolean('notifica_scadenze')->default(true)->after('notifica_invito_accettato');
        });
    }

    public function down(): void
    {
        Schema::table('aa_studi', function (Blueprint $table) {
            $table->dropColumn([
                'api_key',
                'codice_fiscale',
                'sito_web',
                'notifica_kpi_critico',
                'notifica_report_settimanale',
                'notifica_invito_accettato',
                'notifica_scadenze'
            ]);
        });
    }
};
