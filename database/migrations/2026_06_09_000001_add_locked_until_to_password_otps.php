<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_otps', function (Blueprint $table) {
            // When set and in the future, OTP requests/verifies for this
            // email_hash are blocked (anti-brute-force, 30-min lock).
            $table->timestamp('locked_until')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('password_otps', function (Blueprint $table) {
            $table->dropColumn('locked_until');
        });
    }
};
