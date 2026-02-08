<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aa_aziende_cliente', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('studio_id');
            $table->string('nome', 255);
            $table->string('p_iva', 20)->nullable();
            $table->string('codice_fiscale', 20)->nullable();
            $table->string('settore_ateco', 20)->nullable();
            $table->text('indirizzo')->nullable();
            $table->string('citta', 100)->nullable();
            $table->string('cap', 10)->nullable();
            $table->string('provincia', 5)->nullable();
            $table->string('email_referente', 255)->nullable();
            $table->string('telefono_referente', 50)->nullable();
            $table->text('note')->nullable();
            $table->boolean('attiva')->default(true);
            $table->timestamps();

            $table->index('studio_id');
            $table->index('p_iva');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aa_aziende_cliente');
    }
};
