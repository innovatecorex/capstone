<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrance_test_results', function (Blueprint $table) {
            $table->decimal('nv_pct', 5, 2)->nullable()->after('nv_score');
            $table->decimal('v_pct',  5, 2)->nullable()->after('v_score');
        });
    }

    public function down(): void
    {
        Schema::table('entrance_test_results', function (Blueprint $table) {
            $table->dropColumn(['nv_pct', 'v_pct']);
        });
    }
};
