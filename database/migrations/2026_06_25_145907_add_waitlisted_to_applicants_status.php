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
        // MySQL requires re-declaring the full enum to add a value
        \DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM(
            'pending',
            'under_review',
            'waitlisted',
            'accepted',
            'rejected',
            'eligible_for_enrollment',
            'enrolled'
        ) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM(
            'pending',
            'under_review',
            'accepted',
            'rejected',
            'eligible_for_enrollment',
            'enrolled'
        ) NOT NULL DEFAULT 'pending'");
    }
};
