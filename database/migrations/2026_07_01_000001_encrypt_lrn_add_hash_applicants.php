<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * F2 — Encrypt Applicant LRN at rest (mirrors users table from F1).
     *
     * lrn was string(12) with a plain index — too narrow for AES ciphertext
     * and TEXT columns can't carry an un-prefixed index. We widen to TEXT,
     * drop the old index, and add lrn_hash (SHA-256) for exact-match lookups.
     * Run `php artisan lrn:backfill` after migrating to encrypt existing rows.
     */
    public function up(): void
    {
        $indexes = collect(DB::select("SHOW INDEX FROM `applicants` WHERE Key_name = 'applicants_lrn_index'"));
        if ($indexes->isNotEmpty()) {
            DB::statement('ALTER TABLE `applicants` DROP INDEX `applicants_lrn_index`');
        }

        DB::statement('ALTER TABLE `applicants` MODIFY `lrn` TEXT NULL');

        Schema::table('applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('applicants', 'lrn_hash')) {
                $table->string('lrn_hash', 64)->nullable()->after('lrn');
                $table->index('lrn_hash', 'applicants_lrn_hash_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (Schema::hasColumn('applicants', 'lrn_hash')) {
                $table->dropIndex('applicants_lrn_hash_index');
                $table->dropColumn('lrn_hash');
            }
        });
    }
};
