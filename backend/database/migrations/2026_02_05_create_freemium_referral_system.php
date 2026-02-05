<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations - Sistema Freemium e Referral
     *
     * Modello stile Dropbox:
     * - Base: 1 azienda, 3 mesi di storico visibile
     * - Ogni referral: +3 mesi di storico (max 12 totali)
     * - 3 referral: sblocca export PDF
     * - 5 referral: sblocca alert automatici
     */
    public function up(): void
    {
        // Add freemium columns to aa_users
        if (!Schema::hasColumn('aa_users', 'referral_code')) {
            Schema::table('aa_users', function (Blueprint $table) {
                $table->string('referral_code', 20)->nullable()->unique()->after('email');
                $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code');
                $table->integer('storico_mesi_extra')->default(0)->after('referred_by');
                $table->integer('referral_count')->default(0)->after('storico_mesi_extra');
            });
        }

        // Create referrals tracking table
        if (!Schema::hasTable('aa_referrals')) {
            Schema::create('aa_referrals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('referrer_id');
                $table->unsignedBigInteger('referred_id');
                $table->timestamp('created_at');

                $table->foreign('referrer_id')->references('id')->on('aa_users')->onDelete('cascade');
                $table->foreign('referred_id')->references('id')->on('aa_users')->onDelete('cascade');
                $table->unique(['referrer_id', 'referred_id']);
            });
        }

        // Generate referral codes for existing users who don't have one
        $users = DB::table('aa_users')->whereNull('referral_code')->get();
        foreach ($users as $user) {
            $code = strtoupper(\Illuminate\Support\Str::random(8));
            // Ensure uniqueness
            while (DB::table('aa_users')->where('referral_code', $code)->exists()) {
                $code = strtoupper(\Illuminate\Support\Str::random(8));
            }
            DB::table('aa_users')->where('id', $user->id)->update(['referral_code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aa_referrals');

        Schema::table('aa_users', function (Blueprint $table) {
            $table->dropColumn(['referral_code', 'referred_by', 'storico_mesi_extra', 'referral_count']);
        });
    }
};
