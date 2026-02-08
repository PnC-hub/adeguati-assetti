<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aa_users', function (Blueprint $table) {
            $table->enum('tipo_utente', ['imprenditore', 'consulente', 'cliente_readonly'])->default('imprenditore')->after('email');
            $table->unsignedBigInteger('studio_id')->nullable()->after('tipo_utente');
            $table->unsignedBigInteger('azienda_cliente_id')->nullable()->after('studio_id');
        });
    }

    public function down(): void
    {
        Schema::table('aa_users', function (Blueprint $table) {
            $table->dropColumn(['tipo_utente', 'studio_id', 'azienda_cliente_id']);
        });
    }
};
