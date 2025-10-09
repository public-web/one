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
        Schema::table('users', function (Blueprint $table) {
            // Index for filtering active users (used in dashboard and user listings)
            $table->index('active');

            // Index for expiration checks (used in scheduled command and queries)
            $table->index('expires_at');

            // Composite index for the common query: active users with expiration date
            $table->index(['active', 'expires_at'], 'users_active_expires_at_index');

            // Index for soft deletes (deleted_at is already indexed by SoftDeletes trait, but explicit is better)
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['expires_at']);
            $table->dropIndex('users_active_expires_at_index');
            $table->dropIndex(['deleted_at']);
        });
    }
};
