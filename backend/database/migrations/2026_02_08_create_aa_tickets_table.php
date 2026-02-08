<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('aa_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50);
            $table->string('pagina', 255)->nullable();
            $table->text('descrizione');
            $table->string('email_risposta', 255)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_email', 255)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->enum('stato', ['aperto', 'in_lavorazione', 'risolto', 'chiuso'])->default('aperto');
            $table->text('risposta')->nullable();
            $table->timestamps();

            $table->index('stato');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aa_tickets');
    }
};
