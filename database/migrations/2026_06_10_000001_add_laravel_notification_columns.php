<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The notifications table was rebuilt with an auto-increment id and custom
     * columns (user_id/title/body) for Notification::create() calls. But some
     * features use Laravel's ->notify() database channel, which needs
     * notifiable_type / notifiable_id / data columns. This adds those as
     * nullable so BOTH notification styles work against the same table.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'notifiable_type')) {
                $table->string('notifiable_type')->nullable()->after('type');
            }
            if (!Schema::hasColumn('notifications', 'notifiable_id')) {
                $table->unsignedBigInteger('notifiable_id')->nullable()->after('notifiable_type');
            }
            if (!Schema::hasColumn('notifications', 'data')) {
                $table->text('data')->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['notifiable_type', 'notifiable_id', 'data']);
        });
    }
};
