<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Two schema additions that wire up the "reserved-slot, pay-to-activate" flow:
 *
 *  1. enrollments.status gets a new 'pending_payment' value.
 *     When a registrar clicks "Create Student Account", the student is assigned
 *     to a section immediately (so they can see it on their dashboard) but the
 *     enrollment row sits at 'pending_payment'. Grade shells are NOT created yet.
 *     The registrar confirms the payment → enrollment flips to 'enrolled' →
 *     grade shells are created → applicant status becomes 'enrolled'.
 *
 *  2. applicants.user_id links an applicant to the user account that was created
 *     for them. Needed so PaymentController::confirm() can find and update the
 *     applicant record without fragile name/LRN matching.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        // ── 1. Add 'pending_payment' to enrollments.status ─────────────────
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending_payment','enrolled','dropped','transferred','completed') NOT NULL DEFAULT 'enrolled'");
        }
        // SQLite has no CHECK constraint on this column (it was a Blueprint::enum
        // which compiles to TEXT on SQLite), so no change needed there.

        // ── 2. Add user_id FK to applicants ────────────────────────────────
        Schema::table('applicants', function (Blueprint $table) {
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // Flip any pending_payment rows back to enrolled before removing the value
            DB::statement("UPDATE enrollments SET status = 'enrolled' WHERE status = 'pending_payment'");
            DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('enrolled','dropped','transferred','completed') NOT NULL DEFAULT 'enrolled'");
        }

        Schema::table('applicants', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
