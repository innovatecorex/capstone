<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Grade Eratum (D2): when a LOCKED grade is corrected, we preserve the
     * original value and a full audit of who/when/why — the digital equivalent
     * of the school's Grade Eratum form. These columns back Grade::applyCorrection().
     */
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            if (!Schema::hasColumn('grades', 'previous_final_grade')) {
                $table->decimal('previous_final_grade', 5, 2)->nullable()->after('final_grade');
            }
            if (!Schema::hasColumn('grades', 'corrected_by')) {
                $table->unsignedBigInteger('corrected_by')->nullable()->after('previous_final_grade');
            }
            if (!Schema::hasColumn('grades', 'corrected_at')) {
                $table->timestamp('corrected_at')->nullable()->after('corrected_by');
            }
            if (!Schema::hasColumn('grades', 'correction_reason')) {
                $table->text('correction_reason')->nullable()->after('corrected_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn(['previous_final_grade', 'corrected_by', 'corrected_at', 'correction_reason']);
        });
    }
};
