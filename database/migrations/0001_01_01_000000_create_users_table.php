<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Intentionally EMPTY. All tables that the stock Laravel migration
     * would normally create are instead defined by the EncryptEd migrations:
     *   - users                  -> 2024_01_01_000001_create_users_table.php
     *   - password_reset_tokens  -> 2024_01_01_000004_create_sessions_and_tokens_table.php
     *   - sessions               -> 2024_01_01_000004_create_sessions_and_tokens_table.php
     *
     * This file is kept (not deleted) so the migration history stays intact.
     */
    public function up(): void
    {
        // no-op
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // no-op
    }
};
