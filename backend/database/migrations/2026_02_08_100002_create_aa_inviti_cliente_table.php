<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aa_inviti_cliente', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('azienda_cliente_id');
            $table->string('email', 255);
            $table->string('token', 64)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('stato', ['pending', 'accepted', 'revoked'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('accepted_at')->nullable();

            $table->index('token');
            $table->index('email');
            $table->index('azienda_cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aa_inviti_cliente');
    }
};
