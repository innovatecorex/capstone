<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_requirement_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id');
            $table->string('requirement_key', 64);
            $table->boolean('is_submitted')->default(false);
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->foreign('applicant_id')->references('id')->on('applicants')->cascadeOnDelete();
            $table->foreign('checked_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['applicant_id', 'requirement_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_requirement_checks');
    }
};
