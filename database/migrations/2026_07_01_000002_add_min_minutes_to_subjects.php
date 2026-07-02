<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add subjects.min_minutes — the minimum allowable block length for this
     * subject when scheduling. NULL means "use the global default from config".
     *
     * Examples: home room = 45, 1-hour subject = 60, double period = 120.
     * The global default (config academic.schedule_min_minutes) is 60 min.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedSmallInteger('min_minutes')
                  ->nullable()
                  ->after('credits')
                  ->comment('Min schedule block in minutes; null = use global config default');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('min_minutes');
        });
    }
};
