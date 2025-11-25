<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create table for tracking user import/export operations
     * Provides full traceability and audit trail for batch operations
     */
    public function up(): void
    {
        Schema::create('user_import_exports', function (Blueprint $table) {
            $table->id();

            // User who initiated the operation
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who initiated the import/export');

            // Operation type and status
            $table->enum('type', ['import', 'export'])
                ->comment('Type of operation');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])
                ->default('pending')
                ->comment('Current status of the operation');

            // File information
            $table->string('file_path', 500)->nullable()
                ->comment('Storage path to the file');
            $table->string('original_filename', 255)->nullable()
                ->comment('Original filename provided by user');
            $table->string('format', 10)->nullable()
                ->comment('File format: xlsx, csv, etc.');

            // Export-specific data
            $table->json('filters')->nullable()
                ->comment('Filters applied for export (JSON)');

            // Import/Export results
            $table->unsignedInteger('total_rows')->nullable()
                ->comment('Total rows processed');
            $table->unsignedInteger('success_count')->default(0)
                ->comment('Number of successful operations');
            $table->unsignedInteger('error_count')->default(0)
                ->comment('Number of failed operations');

            // Error tracking
            $table->text('error_message')->nullable()
                ->comment('Error message if operation failed');
            $table->json('failures')->nullable()
                ->comment('Detailed failure information (JSON)');

            // Download information (for exports)
            $table->string('download_url', 500)->nullable()
                ->comment('Temporary download URL for completed exports');
            $table->timestamp('download_expires_at')->nullable()
                ->comment('When the download URL expires');

            // Timing information
            $table->timestamp('started_at')->nullable()
                ->comment('When processing started');
            $table->timestamp('completed_at')->nullable()
                ->comment('When processing completed');

            $table->timestamps();

            // Indexes for common queries
            $table->index(['user_id', 'type', 'status'], 'idx_user_type_status');
            $table->index(['status', 'created_at'], 'idx_status_created');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_import_exports');
    }
};
