<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('afts5498_aa_email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('email_type', 50); // welcome, day3, day7, day10, day13
            $table->timestamp('sent_at');
            $table->string('status', 20)->default('sent'); // sent, failed, bounced
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'email_type']);
            $table->foreign('user_id')->references('id')->on('afts5498_aa_users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('afts5498_aa_email_logs');
    }
};
