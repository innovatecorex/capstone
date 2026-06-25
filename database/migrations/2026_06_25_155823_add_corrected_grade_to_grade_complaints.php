<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('grade_complaints', function (Blueprint $table) {
            $table->decimal('corrected_grade', 5, 2)->nullable()->after('grade_id');
            $table->timestamp('grade_corrected_at')->nullable()->after('responded_at');
        });
    }

    public function down(): void
    {
        Schema::table('grade_complaints', function (Blueprint $table) {
            $table->dropColumn(['corrected_grade', 'grade_corrected_at']);
        });
    }
};
