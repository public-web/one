<?php

namespace App\Jobs\Users;

use App\Models\User;
use App\Models\UserImportExport;
use App\Notifications\Users\UsersExportCompleted;
use App\Services\UserImportExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportUsersJob implements ShouldQueue
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
    public function handle(): void
    {
        $operation = UserImportExport::findOrFail($this->operationId);

        try {
            // Mark operation as processing
            $operation->markAsProcessing();

            // Generate filename
            $filename = 'users_export_' . now()->format('Y-m-d_His') . '.' . $operation->format;
            $path = 'exports/' . $filename;

            // Export to storage (instead of direct download)
            $export = new \App\Exports\UsersExport($operation->filters ?? []);
            \Maatwebsite\Excel\Facades\Excel::store($export, $path, 'public');

            // Get row count from export
            $rowCount = $export->getRowCount();

            // Generate download URL (public storage)
            $downloadUrl = Storage::disk('public')->url($path);

            // Mark operation as completed with results
            $operation->markAsCompleted([
                'file_path' => $path,
                'original_filename' => $filename,
                'download_url' => $downloadUrl,
                'download_expires_at' => now()->addHour(),
                'total_rows' => $rowCount,
                'success_count' => $rowCount,
            ]);

            // Notify user with download link
            $operation->user->notify(new UsersExportCompleted(
                true,
                $downloadUrl,
                $filename
            ));

            logger()->info('Users export job completed', [
                'operation_id' => $operation->id,
                'auth_user_id' => $operation->user_id,
                'format' => $operation->format,
                'file' => $filename,
                'rows' => $rowCount,
            ]);
        } catch (\Throwable $e) {
            // Mark operation as failed
            $operation->markAsFailed($e->getMessage());

            logger()->error('Users export job failed', [
                'operation_id' => $operation->id,
                'auth_user_id' => $operation->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Notify user of failure
            $operation->user->notify(new UsersExportCompleted(
                false,
                null,
                null,
                $e->getMessage()
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
                'La exportación falló después de múltiples intentos: ' . $exception->getMessage()
            );

            logger()->error('Users export job permanently failed', [
                'operation_id' => $operation->id,
                'auth_user_id' => $operation->user_id,
                'error' => $exception->getMessage(),
            ]);

            // Notify user
            $operation->user->notify(new UsersExportCompleted(
                false,
                null,
                null,
                'La exportación falló después de múltiples intentos'
            ));
        }
    }
}
