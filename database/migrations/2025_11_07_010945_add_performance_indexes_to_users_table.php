<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add performance indexes for common query patterns in UserController:
     * - Search by name/email
     * - Filter by active status
     * - Filter by deleted (soft deletes)
     * - Filter by expiring users
     * - Order by creation date
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Individual indexes for frequently filtered columns
            if (!$this->indexExists('users', 'idx_users_name')) {
                $table->index('name', 'idx_users_name');
            }
            if (!$this->indexExists('users', 'idx_users_active')) {
                $table->index('active', 'idx_users_active');
            }
            if (!$this->indexExists('users', 'idx_users_deleted_at')) {
                $table->index('deleted_at', 'idx_users_deleted_at');
            }
            if (!$this->indexExists('users', 'idx_users_expires_at')) {
                $table->index('expires_at', 'idx_users_expires_at');
            }
            if (!$this->indexExists('users', 'idx_users_created_at')) {
                $table->index('created_at', 'idx_users_created_at');
            }

            // Composite index for common query: active users that are not deleted
            // Covers: ->where('active', true)->whereNull('deleted_at')
            if (!$this->indexExists('users', 'idx_users_active_deleted')) {
                $table->index(['active', 'deleted_at'], 'idx_users_active_deleted');
            }

            // Composite index for expiring users query
            // Covers: ->whereNotNull('expires_at')->where('expires_at', '<=', now())
            if (!$this->indexExists('users', 'idx_users_expires_active')) {
                $table->index(['expires_at', 'active'], 'idx_users_expires_active');
            }

            // Composite index for listing queries with filters
            // Covers: ->whereNull('deleted_at')->where('active', 1)->orderBy('created_at')
            if (!$this->indexExists('users', 'idx_users_list_query')) {
                $table->index(['deleted_at', 'active', 'created_at'], 'idx_users_list_query');
            }
        });

        // Add index to model_has_roles for role filtering (Spatie Permission package)
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                // Composite index for user role lookups
                // Covers: ->where('model_type', User::class)->where('model_id', $userId)
                if (!$this->indexExists('model_has_roles', 'idx_model_role_lookup')) {
                    $table->index(['model_type', 'model_id', 'role_id'], 'idx_model_role_lookup');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_name');
            $table->dropIndex('idx_users_active');
            $table->dropIndex('idx_users_deleted_at');
            $table->dropIndex('idx_users_expires_at');
            $table->dropIndex('idx_users_created_at');
            $table->dropIndex('idx_users_active_deleted');
            $table->dropIndex('idx_users_expires_active');
            $table->dropIndex('idx_users_list_query');
        });

        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                if ($this->indexExists('model_has_roles', 'idx_model_role_lookup')) {
                    $table->dropIndex('idx_model_role_lookup');
                }
            });
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            $result = $connection->select(
                "SELECT COUNT(*) as count
                 FROM sqlite_master
                 WHERE type = 'index'
                 AND tbl_name = ?
                 AND name = ?",
                [$table, $index]
            );
        } else {
            // MySQL, MariaDB, PostgreSQL compatible
            $database = $connection->getDatabaseName();
            $result = $connection->select(
                "SELECT COUNT(*) as count
                 FROM information_schema.statistics
                 WHERE table_schema = ?
                 AND table_name = ?
                 AND index_name = ?",
                [$database, $table, $index]
            );
        }

        return $result[0]->count > 0;
    }
};
