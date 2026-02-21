<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend piano ENUM in aa_users to include new business model plans
        DB::statement("ALTER TABLE aa_users MODIFY COLUMN piano ENUM('trial','free','starter','professional','studio','business','commercialista_free','impresa49') DEFAULT 'free'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE aa_users MODIFY COLUMN piano ENUM('trial','free','starter','professional','studio','business') DEFAULT 'free'");
    }
};
