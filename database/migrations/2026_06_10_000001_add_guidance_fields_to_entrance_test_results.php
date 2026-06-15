<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrance_test_results', function (Blueprint $table) {
            $table->string('incoming_level', 50)->nullable()->after('administered_by');

            // Admission Test – Non-Verbal
            $table->decimal('nv_score',       6, 2)->nullable()->after('incoming_level');
            $table->decimal('nv_max',         6, 2)->nullable()->after('nv_score');
            $table->string('nv_descriptive', 100)->nullable()->after('nv_max');

            // Admission Test – Verbal
            $table->decimal('v_score',        6, 2)->nullable()->after('nv_descriptive');
            $table->decimal('v_max',          6, 2)->nullable()->after('v_score');
            $table->string('v_descriptive',  100)->nullable()->after('v_max');

            // Academic Test – Filipino
            $table->decimal('acad_filipino_score', 6, 2)->nullable()->after('v_descriptive');
            $table->decimal('acad_filipino_pct',   5, 2)->nullable()->after('acad_filipino_score');
            $table->string('acad_filipino_desc',  100)->nullable()->after('acad_filipino_pct');

            // Academic Test – English
            $table->decimal('acad_english_score', 6, 2)->nullable()->after('acad_filipino_desc');
            $table->decimal('acad_english_pct',   5, 2)->nullable()->after('acad_english_score');
            $table->string('acad_english_desc',  100)->nullable()->after('acad_english_pct');

            // Academic Test – Mathematics
            $table->decimal('acad_math_score', 6, 2)->nullable()->after('acad_english_desc');
            $table->decimal('acad_math_pct',   5, 2)->nullable()->after('acad_math_score');
            $table->string('acad_math_desc',  100)->nullable()->after('acad_math_pct');

            // Academic Test – Science
            $table->decimal('acad_science_score', 6, 2)->nullable()->after('acad_math_desc');
            $table->decimal('acad_science_pct',   5, 2)->nullable()->after('acad_science_score');
            $table->string('acad_science_desc',  100)->nullable()->after('acad_science_pct');

            // Interview / Evaluation
            $table->string('interviewer_name', 200)->nullable()->after('acad_science_desc');
            $table->date('interview_date')->nullable()->after('interviewer_name');
        });
    }

    public function down(): void
    {
        Schema::table('entrance_test_results', function (Blueprint $table) {
            $table->dropColumn([
                'incoming_level',
                'nv_score', 'nv_max', 'nv_descriptive',
                'v_score', 'v_max', 'v_descriptive',
                'acad_filipino_score', 'acad_filipino_pct', 'acad_filipino_desc',
                'acad_english_score',  'acad_english_pct',  'acad_english_desc',
                'acad_math_score',     'acad_math_pct',     'acad_math_desc',
                'acad_science_score',  'acad_science_pct',  'acad_science_desc',
                'interviewer_name', 'interview_date',
            ]);
        });
    }
};
