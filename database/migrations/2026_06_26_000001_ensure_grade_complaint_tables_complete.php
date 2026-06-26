<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure complaint_attachments table exists
        if (!Schema::hasTable('complaint_attachments')) {
            Schema::create('complaint_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('complaint_id')
                      ->constrained('grade_complaints')
                      ->cascadeOnDelete();
                $table->string('file_path', 500);
                $table->string('original_name', 255);
                $table->string('mime_type', 100)->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->timestamps();
                $table->index('complaint_id');
            });
        }

        // Ensure corrected_grade column exists on grade_complaints
        if (!Schema::hasColumn('grade_complaints', 'corrected_grade')) {
            Schema::table('grade_complaints', function (Blueprint $table) {
                $table->decimal('corrected_grade', 5, 2)->nullable()->after('grade_id');
            });
        }

        // Ensure grade_corrected_at column exists on grade_complaints
        if (!Schema::hasColumn('grade_complaints', 'grade_corrected_at')) {
            Schema::table('grade_complaints', function (Blueprint $table) {
                $table->timestamp('grade_corrected_at')->nullable()->after('responded_at');
            });
        }

        // Ensure forwarded_to_teacher is in the status enum
        try {
            \DB::statement("ALTER TABLE grade_complaints MODIFY COLUMN status ENUM(
                'pending','under_review','forwarded_to_teacher','resolved','dismissed'
            ) NOT NULL DEFAULT 'pending'");
        } catch (\Exception $e) {
            // Already has forwarded_to_teacher or DB doesn't support this — safe to skip
        }
    }

    public function down(): void
    {
        // Intentionally left minimal — do not drop tables in a safety migration
    }
};
