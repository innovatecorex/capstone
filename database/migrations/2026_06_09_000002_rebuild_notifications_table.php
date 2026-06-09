<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original notifications table was created with a UUID primary key
     * (Laravel's default notifiable structure) plus unused `data` / `notifiable`
     * columns, but the app uses it as a simple per-user notification table with
     * an auto-incrementing id and user_id/title/body. Inserts failed with
     * "Field 'id' doesn't have a default value" because the UUID id has no
     * auto-increment.
     *
     * This rebuilds the table with the correct structure. (Notification rows
     * are transient, so dropping existing ones is safe.)
     */
    public function up(): void
    {
        Schema::dropIfExists('notifications');

        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // auto-incrementing big integer primary key
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->default('general');
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'read_at'], 'notifications_user_read_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');

        // Recreate the original UUID-based table on rollback
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }
};
