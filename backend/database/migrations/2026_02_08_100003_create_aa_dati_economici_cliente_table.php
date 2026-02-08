<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aa_dati_economici_cliente', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('azienda_cliente_id');
            $table->integer('anno');
            $table->integer('mese');
            $table->decimal('patrimonio_netto', 15, 2)->nullable();
            $table->decimal('totale_attivo', 15, 2)->nullable();
            $table->decimal('totale_debiti', 15, 2)->nullable();
            $table->decimal('debiti_finanziari', 15, 2)->nullable();
            $table->decimal('debiti_tributari', 15, 2)->nullable();
            $table->decimal('ricavi', 15, 2)->nullable();
            $table->decimal('oneri_finanziari', 15, 2)->nullable();
            $table->decimal('cash_flow_operativo', 15, 2)->nullable();
            $table->decimal('rata_debiti', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['azienda_cliente_id', 'anno', 'mese'], 'unique_periodo_cliente');
            $table->index('azienda_cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aa_dati_economici_cliente');
    }
};
