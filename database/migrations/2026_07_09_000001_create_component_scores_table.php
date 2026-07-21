<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * component_scores
 *
 * Individual raw scores that faculty enter for a student under a single grade
 * component (e.g. four separate Homework scores). The page auto-averages them;
 * faculty then manually enter that average into the gradebook. This table is
 * purely a calculator/worksheet store — it does NOT feed the grades table.
 *
 * One row = one raw score, for one student, one component, one item.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('component_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_subject_id')->constrained('section_subjects')->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('grading_quarter_id')->constrained('grading_quarters')->cascadeOnDelete();
            $table->string('component', 10);          // op / hw / ass / pr / aq / alt / qe
            $table->string('item_label', 60)->nullable(); // e.g. "HW #1"
            $table->decimal('score', 6, 2)->nullable();
            $table->timestamps();

            $table->index(['section_subject_id', 'grading_quarter_id', 'component'], 'cs_ss_quarter_comp_idx');
            $table->index('enrollment_id', 'cs_enrollment_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('component_scores');
    }
};
