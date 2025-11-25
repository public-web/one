<?php

namespace App\Jobs\Users;

use App\Models\User;
use App\Models\UserImportExport;
use App\Notifications\Users\UsersImportCompleted;
use App\Services\UserImportExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ImportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Delete the job if its models no longer exist.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @param int $operationId UserImportExport operation ID
     */
    public function __construct(
        public int $operationId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(UserImportExportService $importService): void
    {
        $operation = UserImportExport::findOrFail($this->operationId);

        try {
            // Mark operation as processing
            $operation->markAsProcessing();

            // Get file from storage
            $file = Storage::get($operation->file_path);
            $tempPath = tempnam(sys_get_temp_dir(), 'import_');
            file_put_contents($tempPath, $file);

            // Create UploadedFile instance
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                $operation->original_filename ?? basename($operation->file_path),
                mime_content_type($tempPath),
                null,
                true
            );

            // Process import
            $result = $importService->importUsers($uploadedFile);

            // Clean up temp file
            @unlink($tempPath);

            // Clean up storage file
            Storage::delete($operation->file_path);

            // Mark operation as completed with results
            $operation->markAsCompleted([
                'total_rows' => ($result['success_count'] ?? 0) + ($result['error_count'] ?? 0),
                'success_count' => $result['success_count'] ?? 0,
                'error_count' => $result['error_count'] ?? 0,
                'failures' => $result['failures'] ?? [],
            ]);

            // Notify user of completion
            $operation->user->notify(new UsersImportCompleted(
                $result['success'],
                $result['success_count'] ?? 0,
                $result['error_count'] ?? 0,
                $result['failures'] ?? []
            ));

            logger()->info('Users import job completed', [
                'operation_id' => $operation->id,
                'auth_user_id' => $operation->user_id,
                'success' => $result['success'],
                'imported' => $result['success_count'] ?? 0,
            ]);
        } catch (\Throwable $e) {
            // Mark operation as failed
            $operation->markAsFailed($e->getMessage());

            logger()->error('Users import job failed', [
                'operation_id' => $operation->id,
                'auth_user_id' => $operation->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Notify user of failure
            $operation->user->notify(new UsersImportCompleted(
                false,
                0,
                1,
                [['error' => $e->getMessage()]]
            ));

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $operation = UserImportExport::find($this->operationId);

        if ($operation) {
            // Mark operation as failed
            $operation->markAsFailed(
                'El trabajo de importación falló después de múltiples intentos: ' . $exception->getMessage()
            );

            logger()->error('Users import job permanently failed', [
                'operation_id' => $operation->id,
                'auth_user_id' => $operation->user_id,
                'error' => $exception->getMessage(),
            ]);

            // Notify user
            $operation->user->notify(new UsersImportCompleted(
                false,
                0,
                1,
                [['error' => 'El trabajo de importación falló después de múltiples intentos']]
            ));

            // Clean up storage file
            if ($operation->file_path) {
                Storage::delete($operation->file_path);
            }
        }
    }
}
