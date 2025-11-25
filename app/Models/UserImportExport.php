<?php

namespace App\Models;

use App\Enums\ImportExportStatus;
use App\Enums\ImportExportType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * UserImportExport Model
 *
 * Tracks all user import/export operations for audit trail and monitoring
 *
 * State Flow Diagram:
 * ==================
 *
 *     [Pending] ──────> [Processing] ──────> [Completed] (final)
 *         │                  │
 *         │                  │
 *         └────> [Failed] <──┘
 *                    │
 *                    └────> [Processing] (retry)
 *
 * Valid State Transitions:
 * - Pending → Processing (normal flow)
 * - Pending → Failed (validation error before processing)
 * - Processing → Completed (successful completion)
 * - Processing → Failed (error during processing)
 * - Failed → Processing (retry operation)
 * - Completed → (no transitions - final state)
 *
 * @property int $id
 * @property int $user_id
 * @property ImportExportType $type
 * @property ImportExportStatus $status
 * @property string|null $file_path
 * @property string|null $original_filename
 * @property string|null $format
 * @property array|null $filters
 * @property int|null $total_rows
 * @property int $success_count
 * @property int $error_count
 * @property string|null $error_message
 * @property array|null $failures
 * @property string|null $download_url
 * @property \Illuminate\Support\Carbon|null $download_expires_at
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class UserImportExport extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The table associated with the model.
     */
    protected $table = 'user_import_exports';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'type',
        'status',
        'file_path',
        'original_filename',
        'format',
        'filters',
        'total_rows',
        'success_count',
        'error_count',
        'error_message',
        'failures',
        'download_url',
        'download_expires_at',
        'started_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'type' => ImportExportType::class,
        'status' => ImportExportStatus::class,
        'filters' => 'array',
        'failures' => 'array',
        'download_expires_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_rows' => 'integer',
        'success_count' => 'integer',
        'error_count' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'duration_for_humans',
        'success_rate',
        'type_label',
        'status_label',
        'status_color',
        'is_download_valid',
    ];

    /**
     * Configure activity logging options
     *
     * Logs all changes to import/export operations for audit trail
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'type',
                'status',
                'format',
                'total_rows',
                'success_count',
                'error_count',
                'error_message',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('import-export')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => "Operación de {$this->type_label} creada",
                'updated' => "Operación de {$this->type_label} actualizada",
                'deleted' => "Operación de {$this->type_label} eliminada",
                default => $eventName,
            });
    }

    /**
     * Boot the model and register event listeners
     */
    protected static function booted(): void
    {
        // Log when operation is created
        static::created(function (UserImportExport $model) {
            activity('import-export')
                ->performedOn($model)
                ->causedBy($model->user)
                ->withProperties([
                    'type' => $model->type_label,
                    'format' => $model->format,
                    'status' => $model->status_label,
                ])
                ->log("Nueva operación de {$model->type_label} iniciada");
        });

        // Log status changes
        static::updating(function (UserImportExport $model) {
            if ($model->isDirty('status')) {
                // Get original status - it's already casted to Enum
                $oldStatus = $model->getOriginal('status');
                $newStatus = $model->status;

                activity('import-export')
                    ->performedOn($model)
                    ->causedBy($model->user)
                    ->withProperties([
                        'old_status' => $oldStatus->label(),
                        'new_status' => $newStatus->label(),
                        'type' => $model->type_label,
                    ])
                    ->log("Operación de {$model->type_label} cambió de {$oldStatus->label()} a {$newStatus->label()}");
            }
        });

        // Log when operation is completed
        static::updated(function (UserImportExport $model) {
            if ($model->wasChanged('status') && $model->isCompleted()) {
                activity('import-export')
                    ->performedOn($model)
                    ->causedBy($model->user)
                    ->withProperties([
                        'type' => $model->type_label,
                        'total_rows' => $model->total_rows,
                        'success_count' => $model->success_count,
                        'success_rate' => $model->success_rate,
                        'duration' => $model->duration_for_humans,
                    ])
                    ->log("Operación de {$model->type_label} completada exitosamente ({$model->total_rows} registros procesados)");
            }

            // Log when operation fails
            if ($model->wasChanged('status') && $model->isFailed()) {
                activity('import-export')
                    ->performedOn($model)
                    ->causedBy($model->user)
                    ->withProperties([
                        'type' => $model->type_label,
                        'error_message' => $model->error_message,
                        'error_count' => $model->error_count,
                    ])
                    ->log("Operación de {$model->type_label} falló: {$model->error_message}");
            }
        });

        // Log when operation is deleted
        static::deleted(function (UserImportExport $model) {
            activity('import-export')
                ->performedOn($model)
                ->causedBy(auth()->user())
                ->withProperties([
                    'type' => $model->type_label,
                    'status' => $model->status_label,
                ])
                ->log("Operación de {$model->type_label} eliminada");
        });
    }

    /**
     * Get the user who initiated this operation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Filter by type (import/export)
     */
    public function scopeOfType($query, ImportExportType $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeWithStatus($query, ImportExportStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get only imports
     */
    public function scopeImports($query)
    {
        return $query->where('type', ImportExportType::Import);
    }

    /**
     * Scope: Get only exports
     */
    public function scopeExports($query)
    {
        return $query->where('type', ImportExportType::Export);
    }

    /**
     * Scope: Get pending operations
     */
    public function scopePending($query)
    {
        return $query->where('status', ImportExportStatus::Pending);
    }

    /**
     * Scope: Get processing operations
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', ImportExportStatus::Processing);
    }

    /**
     * Scope: Get completed operations
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', ImportExportStatus::Completed);
    }

    /**
     * Scope: Get failed operations
     */
    public function scopeFailed($query)
    {
        return $query->where('status', ImportExportStatus::Failed);
    }

    /**
     * Scope: Recent operations (last 30 days)
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: Filter by trashed status
     *
     * @param mixed $query
     * @param string|null $filter 'only' = only trashed, 'with' = include trashed, null = exclude trashed (default)
     */
    public function scopeTrashed($query, ?string $filter = null)
    {
        return match ($filter) {
            'only' => $query->onlyTrashed(),
            'with' => $query->withTrashed(),
            default => $query->withoutTrashed(),
        };
    }

    /**
     * Mark operation as started
     *
     * @throws \LogicException if operation is not in pending status
     */
    public function markAsProcessing(): void
    {
        // Validate state transition
        if (!$this->isPending()) {
            throw new \LogicException(
                sprintf(
                    'No se puede marcar como procesando una operación con estado "%s". Solo se pueden procesar operaciones pendientes.',
                    $this->status_label
                )
            );
        }

        $this->update([
            'status' => ImportExportStatus::Processing,
            'started_at' => now(),
        ]);
    }

    /**
     * Mark operation as completed
     *
     * @param array $data Additional data to update (file_path, download_url, etc.)
     * @throws \LogicException if operation is already in a final state
     */
    public function markAsCompleted(array $data = []): void
    {
        // Validate state transition - can't complete if already completed or failed
        if ($this->isCompleted()) {
            throw new \LogicException('No se puede marcar como completado un proceso que ya está completado.');
        }

        if ($this->isFailed()) {
            throw new \LogicException('No se puede marcar como completado un proceso que ha fallado.');
        }

        $this->update(array_merge([
            'status' => ImportExportStatus::Completed,
            'completed_at' => now(),
        ], $data));
    }

    /**
     * Mark operation as failed
     *
     * @param string $errorMessage Error message describing the failure
     * @param array $failures Detailed failure information
     * @throws \LogicException if operation is already completed
     */
    public function markAsFailed(string $errorMessage, array $failures = []): void
    {
        // Validate state transition - can't fail if already completed
        if ($this->isCompleted()) {
            throw new \LogicException('No se puede marcar como fallido un proceso completado.');
        }

        $this->update([
            'status' => ImportExportStatus::Failed,
            'error_message' => $errorMessage,
            'failures' => $failures,
            'completed_at' => now(),
        ]);
    }

    /**
     * Check if operation is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === ImportExportStatus::Completed;
    }

    /**
     * Check if operation failed
     */
    public function isFailed(): bool
    {
        return $this->status === ImportExportStatus::Failed;
    }

    /**
     * Check if operation is still processing
     */
    public function isProcessing(): bool
    {
        return $this->status === ImportExportStatus::Processing;
    }

    /**
     * Check if operation is pending
     */
    public function isPending(): bool
    {
        return $this->status === ImportExportStatus::Pending;
    }

    /**
     * Check if operation is an import
     */
    public function isImport(): bool
    {
        return $this->type === ImportExportType::Import;
    }

    /**
     * Check if operation is an export
     */
    public function isExport(): bool
    {
        return $this->type === ImportExportType::Export;
    }

    /**
     * Check if the operation can be cancelled/deleted
     *
     * Only pending or failed operations can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->isPending() || $this->isFailed();
    }

    /**
     * Check if the operation can be retried
     *
     * Only failed operations can be retried
     */
    public function canBeRetried(): bool
    {
        return $this->isFailed();
    }

    /**
     * Validate state transition
     *
     * @param ImportExportStatus $newStatus
     * @return bool
     */
    public function canTransitionTo(ImportExportStatus $newStatus): bool
    {
        // Define valid state transitions
        $validTransitions = [
            ImportExportStatus::Pending->value => [
                ImportExportStatus::Processing->value,
                ImportExportStatus::Failed->value, // Can fail without processing (e.g., validation error)
            ],
            ImportExportStatus::Processing->value => [
                ImportExportStatus::Completed->value,
                ImportExportStatus::Failed->value,
            ],
            ImportExportStatus::Completed->value => [], // Final state - no transitions allowed
            ImportExportStatus::Failed->value => [
                ImportExportStatus::Processing->value, // Allow retry by going back to processing
            ],
        ];

        $currentStatus = $this->status->value;
        $allowedTransitions = $validTransitions[$currentStatus] ?? [];

        return in_array($newStatus->value, $allowedTransitions);
    }

    /**
     * Retry a failed operation
     *
     * Resets the operation to processing state and clears error information
     *
     * @throws \LogicException if operation cannot be retried
     */
    public function retry(): void
    {
        if (!$this->canBeRetried()) {
            throw new \LogicException(
                sprintf(
                    'No se puede reintentar una operación con estado "%s". Solo se pueden reintentar operaciones fallidas.',
                    $this->status_label
                )
            );
        }

        $this->update([
            'status' => ImportExportStatus::Processing,
            'error_message' => null,
            'failures' => null,
            'started_at' => now(),
            'completed_at' => null,
        ]);
    }

    /**
     * Check if download URL is still valid
     */
    public function isDownloadValid(): bool
    {
        return $this->download_url
            && $this->download_expires_at
            && $this->download_expires_at->isFuture();
    }

    /**
     * Get duration in seconds
     */
    public function getDurationInSeconds(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->completed_at->diffInSeconds($this->started_at);
    }

    /**
     * Get human-readable duration
     */
    public function getDurationForHumans(): ?string
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->started_at->diffForHumans($this->completed_at, true);
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRate(): ?float
    {
        if (!$this->total_rows || $this->total_rows === 0) {
            return null;
        }

        return round(($this->success_count / $this->total_rows) * 100, 2);
    }

    /**
     * Accessor: Get human-readable duration
     */
    public function getDurationForHumansAttribute(): ?string
    {
        return $this->getDurationForHumans();
    }

    /**
     * Accessor: Get success rate percentage
     */
    public function getSuccessRateAttribute(): ?float
    {
        return $this->getSuccessRate();
    }

    /**
     * Accessor: Get type label in Spanish
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    /**
     * Accessor: Get status label in Spanish
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * Accessor: Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    /**
     * Accessor: Check if download URL is still valid
     */
    public function getIsDownloadValidAttribute(): bool
    {
        return $this->isDownloadValid();
    }
}
