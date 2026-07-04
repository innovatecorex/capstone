<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Encrypt username, first_name, last_name and gender at rest (RA 10173),
 * mirroring the email / lrn pattern.
 *
 * AES ciphertext (~180-300 chars) does not fit the original column types:
 *   - username   VARCHAR(100) UNIQUE  → TEXT (uniqueness moves to username_hash)
 *   - first_name VARCHAR(100)         → TEXT
 *   - last_name  VARCHAR(100)         → TEXT
 *   - gender     ENUM('male','female')→ TEXT
 *
 * Exact-match lookups / equality filters use deterministic SHA-256 companions:
 *   - username_hash   — unique  (login lookup)
 *   - first_name_hash — indexed (exact name search)
 *   - last_name_hash  — indexed (exact name search)
 *   - gender_hash     — indexed (equality filters / counts)
 *
 * Existing plaintext rows remain readable via the model's legacy fallback
 * until `php artisan pii:backfill` encrypts them. Raw SQL MODIFY is used for
 * type changes to avoid needing doctrine/dbal.
 *
 * DO NOT auto-run on production. Sequence: backup → migrate → pii:backfill
 * --dry-run → pii:backfill → verify login.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Drop the old UNIQUE index on username (uniqueness moves to hash) ──
        $idx = collect(DB::select("SHOW INDEX FROM `users` WHERE Key_name = 'users_username_unique'"));
        if ($idx->isNotEmpty()) {
            DB::statement('ALTER TABLE `users` DROP INDEX `users_username_unique`');
        }

        // ── Widen encrypted columns to TEXT (raw SQL avoids doctrine/dbal) ────
        DB::statement('ALTER TABLE `users` MODIFY `username` TEXT NOT NULL');
        DB::statement('ALTER TABLE `users` MODIFY `first_name` TEXT NOT NULL');
        DB::statement('ALTER TABLE `users` MODIFY `last_name` TEXT NOT NULL');
        DB::statement('ALTER TABLE `users` MODIFY `gender` TEXT NULL');

        // ── Add hash companion columns ───────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username_hash')) {
                $table->string('username_hash', 64)->nullable()->after('username');
                $table->unique('username_hash', 'users_username_hash_unique');
            }
            if (!Schema::hasColumn('users', 'first_name_hash')) {
                $table->string('first_name_hash', 64)->nullable()->after('first_name');
                $table->index('first_name_hash', 'users_first_name_hash_index');
            }
            if (!Schema::hasColumn('users', 'last_name_hash')) {
                $table->string('last_name_hash', 64)->nullable()->after('last_name');
                $table->index('last_name_hash', 'users_last_name_hash_index');
            }
            if (!Schema::hasColumn('users', 'gender_hash')) {
                $table->string('gender_hash', 64)->nullable()->after('gender');
                $table->index('gender_hash', 'users_gender_hash_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username_hash')) {
                $table->dropUnique('users_username_hash_unique');
                $table->dropColumn('username_hash');
            }
            if (Schema::hasColumn('users', 'first_name_hash')) {
                $table->dropIndex('users_first_name_hash_index');
                $table->dropColumn('first_name_hash');
            }
            if (Schema::hasColumn('users', 'last_name_hash')) {
                $table->dropIndex('users_last_name_hash_index');
                $table->dropColumn('last_name_hash');
            }
            if (Schema::hasColumn('users', 'gender_hash')) {
                $table->dropIndex('users_gender_hash_index');
                $table->dropColumn('gender_hash');
            }
        });

        // Note: username/first_name/last_name stay TEXT and gender stays TEXT on
        // rollback; original types were VARCHAR(100)/ENUM. Re-tightening would
        // fail on already-encrypted rows, so it is intentionally left to a
        // manual step after decryption if ever needed.
    }
};
