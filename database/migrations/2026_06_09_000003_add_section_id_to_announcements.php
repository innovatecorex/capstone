<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'section_id')) {
                // When set, the announcement targets only the students of this
                // section (used by faculty announcing to their own class).
                $table->unsignedBigInteger('section_id')->nullable()->after('target_audience');
                $table->index('section_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('section_id');
        });
    }
};
