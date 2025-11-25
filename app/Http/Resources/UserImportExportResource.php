<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * UserImportExportResource
 *
 * Resource for transforming UserImportExport model to JSON
 * Provides comprehensive operation tracking information
 */
class UserImportExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'type_label' => $this->type_label,
            'status' => $this->status->value,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,

            // User information
            'user' => $this->when($this->relationLoaded('user'), [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ]),

            // File information
            'file_path' => $this->file_path,
            'original_filename' => $this->original_filename,
            'format' => $this->format,

            // Export-specific data
            'filters' => $this->when($this->isExport() && $this->filters, $this->filters),

            // Results
            'total_rows' => $this->total_rows,
            'success_count' => $this->success_count,
            'error_count' => $this->error_count,
            'success_rate' => $this->success_rate,

            // Error tracking
            'error_message' => $this->when($this->isFailed(), $this->error_message),
            'failures' => $this->when(
                $this->isFailed() && $this->failures,
                fn() => array_slice($this->failures, 0, 10) // Limit to first 10 failures for performance
            ),

            // Download information (for exports)
            'download_url' => $this->when(
                $this->isExport() && $this->isCompleted() && $this->isDownloadValid(),
                $this->download_url
            ),
            'download_expires_at' => $this->when(
                $this->isExport() && $this->download_expires_at,
                $this->download_expires_at?->toIso8601String()
            ),
            'is_download_valid' => $this->is_download_valid,

            // Timing information
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'duration_seconds' => $this->getDurationInSeconds(),
            'duration_for_humans' => $this->duration_for_humans,

            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'deleted_at' => $this->when($this->trashed(), $this->deleted_at?->format('Y-m-d H:i:s')),
            'deleted_at_human' => $this->when($this->trashed(), $this->deleted_at?->diffForHumans()),

            // Status flags for easy frontend rendering
            'is_pending' => $this->isPending(),
            'is_processing' => $this->isProcessing(),
            'is_completed' => $this->isCompleted(),
            'is_failed' => $this->isFailed(),
            'is_trashed' => $this->trashed(),
        ];
    }
}
