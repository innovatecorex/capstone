<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->timestamp('finalized_at')->nullable()->after('dropped_at');
            $table->foreignId('finalized_by')->nullable()->after('finalized_at')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['finalized_by']);
            $table->dropColumn(['finalized_at', 'finalized_by']);
        });
    }
};
