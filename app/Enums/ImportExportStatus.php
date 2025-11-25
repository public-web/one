<?php

namespace App\Enums;

/**
 * Import/Export Operation Status Enum
 *
 * Defines the current status of an import/export operation
 */
enum ImportExportStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Processing => 'Procesando',
            self::Completed => 'Completado',
            self::Failed => 'Fallido',
        };
    }

    /**
     * Get badge color for UI
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Processing => 'blue',
            self::Completed => 'green',
            self::Failed => 'red',
        };
    }

    /**
     * Check if operation is in a final state
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::Completed, self::Failed]);
    }

    /**
     * Check if operation is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this, [self::Pending, self::Processing]);
    }

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all cases as associative array [value => label]
     */
    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            fn($carry, $case) => [...$carry, $case->value => $case->label()],
            []
        );
    }
}
