<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * F1 — Encrypt LRN at rest with a searchable hash (mirrors the email pattern).
     *
     * lrn was string(12) UNIQUE — too small for AES ciphertext, and a unique
     * index can't sit on encrypted values. We widen lrn to TEXT, move
     * uniqueness to lrn_hash (SHA-256), and keep both nullable (LRN is
     * student-only). Existing plaintext rows stay readable via the model's
     * legacy fallback until `php artisan lrn:backfill` encrypts them.
     */
    public function up(): void
    {
        // Drop the old unique index on lrn if it exists (name is the Laravel default).
        $indexes = collect(DB::select("SHOW INDEX FROM `users` WHERE Key_name = 'users_lrn_unique'"));
        if ($indexes->isNotEmpty()) {
            DB::statement('ALTER TABLE `users` DROP INDEX `users_lrn_unique`');
        }

        // Widen lrn to hold ciphertext (raw SQL avoids needing doctrine/dbal).
        DB::statement('ALTER TABLE `users` MODIFY `lrn` TEXT NULL');

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'lrn_hash')) {
                $table->string('lrn_hash', 64)->nullable()->after('lrn');
                $table->unique('lrn_hash', 'users_lrn_hash_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'lrn_hash')) {
                $table->dropUnique('users_lrn_hash_unique');
                $table->dropColumn('lrn_hash');
            }
        });
        // Note: lrn stays TEXT on rollback; original column was string(12).
    }
};
