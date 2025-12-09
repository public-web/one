<?php

namespace App\Imports;

use App\Models\BancoProyecto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BancoProyectosImport implements WithMultipleSheets
{
    private $successCount = 0;
    private $errorRows = [];
    private $sheetImport;

    /**
     * Only import the first sheet
     */
    public function sheets(): array
    {
        $this->sheetImport = new BancoProyectosSheetImport($this);

        return [
            0 => $this->sheetImport,
        ];
    }

    public function incrementSuccess()
    {
        $this->successCount++;
    }

    public function addError(array $row, string $error)
    {
        $this->errorRows[] = [
            'row' => $row,
            'error' => $error
        ];
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrors(): array
    {
        return $this->errorRows;
    }

    public function getFailures(): array
    {
        if ($this->sheetImport) {
            return $this->sheetImport->failures()->toArray();
        }
        return [];
    }
}

class BancoProyectosSheetImport implements ToModel, WithStartRow, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    private $parent;

    public function __construct(BancoProyectosImport $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Start reading from row 2 (skipping header row)
     */
    public function startRow(): int
    {
        return 2; // Row 2 (1-indexed) is the first data row
    }

    /**
     * Get column value by index with null/empty handling
     */
    private function getValue(array $row, int $index, $default = null)
    {
        if (!isset($row[$index]) || $row[$index] === null || $row[$index] === '') {
            return $default;
        }

        $value = $row[$index];

        // Convert to string and trim if it's a string
        if (is_string($value)) {
            return trim($value);
        }

        // Convert numbers to strings
        if (is_numeric($value)) {
            return (string) $value;
        }

        return $value;
    }

    public function model(array $row)
    {
        try {
            // Skip empty rows
            if (empty(array_filter($row))) {
                return null;
            }

            // Map columns by index (0-indexed)
            // Column 0: tipo_elemento_civ_rupi
            // Column 1: codigo_elemento
            // Column 2: uso
            // Column 3: area_elemento
            // Column 4: localidad
            // Column 5: upl
            // Column 6: barrio
            // Column 7: tramo_direccion
            // Column 8: eje
            // Column 9: inicio
            // Column 10: fin
            // Column 11: reserva
            // Column 12: estado
            // Column 13: id_contrato
            // Column 14: latitude
            // Column 15: longitude

            $proyecto = BancoProyecto::create([
                'tipo_elemento_civ_rupi' => $this->getValue($row, 0),
                'codigo_elemento' => $this->getValue($row, 1),
                'uso' => $this->getValue($row, 2),
                'area_elemento' => $this->getValue($row, 3),
                'localidad' => $this->getValue($row, 4),
                'upl' => $this->getValue($row, 5),
                'barrio' => $this->getValue($row, 6),
                'tramo_direccion' => $this->getValue($row, 7),
                'eje' => $this->getValue($row, 8),
                'inicio' => $this->getValue($row, 9),
                'fin' => $this->getValue($row, 10),
                'reserva' => $this->getValue($row, 11),
                'estado' => $this->getValue($row, 12),
                'id_contrato' => $this->getValue($row, 13),
                'latitude' => $this->getValue($row, 14),
                'longitude' => $this->getValue($row, 15),
            ]);

            $this->parent->incrementSuccess();

            return $proyecto;
        } catch (\Exception $e) {
            $this->parent->addError($row, $e->getMessage());
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'codigo_elemento' => ['required', 'string', 'max:255', 'unique:banco_proyectos,codigo_elemento'],
            'tipo_elemento_civ_rupi' => ['nullable', 'string', 'max:255'],
            'uso' => ['nullable', 'string', 'max:255'],
            'area_elemento' => ['nullable', 'string', 'max:255'],
            'localidad' => ['nullable', 'string', 'max:255'],
            'upl' => ['nullable', 'string', 'max:255'],
            'barrio' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
            'id_contrato' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'codigo_elemento.required' => 'El código del elemento es requerido.',
            'codigo_elemento.unique' => 'Este código de elemento ya existe.',
        ];
    }
}
