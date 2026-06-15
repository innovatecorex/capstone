<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM('pending','under_review','accepted','rejected','enrolled','eligible_for_enrollment') NOT NULL DEFAULT 'pending'");
            return;
        }

        // SQLite: cannot modify check constraints; swap the column to drop the old constraint.
        // Drop index if it still exists (may have been removed by a previous partial run).
        $indexes = array_column(
            DB::select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name='applicants'"),
            'name'
        );
        if (in_array('app_status_grade_idx', $indexes, true)) {
            Schema::table('applicants', function (Blueprint $table) {
                $table->dropIndex('app_status_grade_idx');
            });
        }

        Schema::table('applicants', function (Blueprint $table) {
            $table->string('status_tmp')->default('pending');
        });
        DB::statement('UPDATE applicants SET status_tmp = status');
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('status')->default('pending');
        });
        DB::statement('UPDATE applicants SET status = status_tmp');
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('status_tmp');
        });

        Schema::table('applicants', function (Blueprint $table) {
            $table->index(['status', 'applying_for_grade'], 'app_status_grade_idx');
        });
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE applicants SET status = 'accepted' WHERE status = 'eligible_for_enrollment'");
            DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM('pending','under_review','accepted','rejected','enrolled') NOT NULL DEFAULT 'pending'");
        }
        // SQLite: no rollback — column has no check constraint anyway
    }
};
