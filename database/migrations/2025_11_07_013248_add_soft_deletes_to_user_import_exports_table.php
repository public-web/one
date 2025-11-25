<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add soft deletes support to user_import_exports table
     * Allows "archiving" old operations instead of permanently deleting them
     */
    public function up(): void
    {
        Schema::table('user_import_exports', function (Blueprint $table) {
            $table->softDeletes();

            // Add index for common queries with soft deletes
            $table->index(['deleted_at', 'created_at'], 'idx_deleted_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_import_exports', function (Blueprint $table) {
            $table->dropIndex('idx_deleted_created');
            $table->dropSoftDeletes();
        });
    }
};
