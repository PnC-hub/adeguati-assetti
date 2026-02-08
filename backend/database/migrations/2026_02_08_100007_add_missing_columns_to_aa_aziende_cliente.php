<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aa_aziende_cliente', function (Blueprint $table) {
            // Only add columns that don't already exist
            // Existing: codice_fiscale, codice_ateco, indirizzo, citta, cap, telefono, email, note
            // Missing: provincia, settore_ateco (codice_ateco exists but different name)
            if (!Schema::hasColumn('aa_aziende_cliente', 'provincia')) {
                $table->string('provincia', 5)->nullable()->after('cap');
            }
            if (!Schema::hasColumn('aa_aziende_cliente', 'settore_ateco')) {
                $table->string('settore_ateco', 20)->nullable()->after('codice_ateco');
            }
            if (!Schema::hasColumn('aa_aziende_cliente', 'email_referente')) {
                $table->string('email_referente', 255)->nullable()->after('email');
            }
            if (!Schema::hasColumn('aa_aziende_cliente', 'telefono_referente')) {
                $table->string('telefono_referente', 50)->nullable()->after('telefono');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aa_aziende_cliente', function (Blueprint $table) {
            $columns = ['provincia', 'settore_ateco', 'email_referente', 'telefono_referente'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('aa_aziende_cliente', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
