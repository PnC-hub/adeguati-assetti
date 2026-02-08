<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aa_studi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nome', 255);
            $table->string('p_iva', 20)->nullable();
            $table->text('indirizzo')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->string('colore_primario', 7)->default('#DC2626');
            $table->boolean('white_label_attivo')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aa_studi');
    }
};
