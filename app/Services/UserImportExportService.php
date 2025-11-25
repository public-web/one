<?php

namespace App\Services;

use App\Events\Users\UsersImported;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Service for handling user data import and export operations
 *
 * Provides methods for:
 * - Exporting users to Excel/CSV
 * - Importing users from Excel/CSV
 * - Downloading import templates
 */
class UserImportExportService
{
    /**
     * Export users to Excel/CSV
     *
     * @param string $format Format of export (xlsx, csv, etc.)
     * @param array $filters Filters to apply to export
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportUsers(string $format = 'xlsx', array $filters = [])
    {
        try {
            $filename = 'users_' . now()->format('Y-m-d_His') . '.' . $format;

            return Excel::download(
                new \App\Exports\UsersExport($filters),
                $filename
            );
        } catch (\Exception $e) {
            logger()->error('Error al exportar usuarios', [
                'format' => $format,
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Error al exportar usuarios: ' . $e->getMessage());
        }
    }

    /**
     * Download import template
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadImportTemplate()
    {
        try {
            $headers = ['name', 'email', 'role', 'active', 'require_2fa', 'expires_at'];
            $sample = [
                ['John Doe', 'john@example.com', 'user', 'true', 'false', '2026-12-31'],
                ['Jane Smith', 'jane@example.com', 'admin', 'true', 'true', ''],
            ];

            $export = new class($headers, $sample) implements \Maatwebsite\Excel\Concerns\FromArray {
                public function __construct(private $headers, private $sample) {}

                public function array(): array
                {
                    return array_merge([$this->headers], $this->sample);
                }
            };

            return Excel::download($export, 'users_import_template.xlsx');
        } catch (\Exception $e) {
            logger()->error('Error al generar template de importación', [
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    /**
     * Import users from uploaded file
     *
     * @param UploadedFile $file
     * @return array Import results with success count and failures
     */
    public function importUsers(UploadedFile $file): array
    {
        $import = new \App\Imports\UsersImport();

        try {
            Excel::import($import, $file);

            $successCount = $import->getSuccessCount();
            $failures = $import->getFailures();
            $errors = $import->getErrors();

            logger()->info('Importación de usuarios completada', [
                'success_count' => $successCount,
                'error_count' => count($failures) + count($errors),
                'filename' => $file->getClientOriginalName(),
            ]);

            // Dispatch users imported event
            UsersImported::dispatch(
                $successCount,
                count($failures) + count($errors),
                array_merge($failures, $errors)
            );

            return [
                'success' => true,
                'success_count' => $successCount,
                'error_count' => count($failures) + count($errors),
                'failures' => $failures,
                'errors' => $errors,
                'message' => "Importación completada: {$successCount} usuario(s) creado(s) exitosamente." .
                    (count($failures) + count($errors) > 0 ? " " . (count($failures) + count($errors)) . " error(es) encontrado(s)." : ""),
            ];
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            logger()->warning('Error de validación en importación de usuarios', [
                'filename' => $file->getClientOriginalName(),
                'failures_count' => count($failures),
                'failures' => $failures,
            ]);

            return [
                'success' => false,
                'failures' => $failures,
                'message' => 'Error de validación en el archivo importado',
            ];
        } catch (\Exception $e) {
            logger()->error('Error al importar usuarios', [
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Error al importar: ' . $e->getMessage());
        }
    }
}
