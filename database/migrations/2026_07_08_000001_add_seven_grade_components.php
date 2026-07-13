<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add the client's official 7-component grade structure to the grades table.
 *
 *   OP  Oral Participation      5%
 *   HW  Homework               10%
 *   ASS Assignment/Seatwork    10%
 *   PR  Project                 5%
 *   AQ                         20%
 *   ALT                        20%
 *   QE  Quarterly Exam         30%
 *
 * The legacy 3 columns (written_work, performance_task, quarterly_assessment)
 * are intentionally left in place so this change is reversible and any old
 * data is preserved.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->decimal('op', 5, 2)->nullable()->after('quarterly_assessment');
            $table->decimal('hw', 5, 2)->nullable()->after('op');
            $table->decimal('ass', 5, 2)->nullable()->after('hw');
            $table->decimal('pr', 5, 2)->nullable()->after('ass');
            $table->decimal('aq', 5, 2)->nullable()->after('pr');
            $table->decimal('alt', 5, 2)->nullable()->after('aq');
            $table->decimal('qe', 5, 2)->nullable()->after('alt');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn(['op', 'hw', 'ass', 'pr', 'aq', 'alt', 'qe']);
        });
    }
};
