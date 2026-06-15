<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('grade_level', 30);
            $table->string('status', 20)->default('pending'); // pending|confirmed|cancelled
            $table->unsignedBigInteger('added_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->foreign('added_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['student_id', 'academic_year_id', 'subject_id'], 'ep_student_year_subject_unique');
            $table->index(['student_id', 'academic_year_id'], 'ep_student_year_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_plans');
    }
};
