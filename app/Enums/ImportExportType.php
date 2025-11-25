<?php

namespace App\Enums;

/**
 * Import/Export Operation Type Enum
 *
 * Defines the type of import/export operation
 */
enum ImportExportType: string
{
    case Import = 'import';
    case Export = 'export';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::Import => 'Importación',
            self::Export => 'Exportación',
        };
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
