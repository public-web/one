<?php

namespace App\Exports;

use App\Models\BancoProyecto;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BancoProyectosExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $filters;
    protected $rowCount = null;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get the row count of the export
     */
    public function getRowCount(): int
    {
        if ($this->rowCount === null) {
            $this->rowCount = $this->query()->count();
        }
        return $this->rowCount;
    }

    public function query()
    {
        $query = BancoProyecto::withTrashed();

        // Apply filters
        if (isset($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('codigo_elemento', 'like', "%{$search}%")
                  ->orWhere('tipo_elemento_civ_rupi', 'like', "%{$search}%")
                  ->orWhere('barrio', 'like', "%{$search}%")
                  ->orWhere('localidad', 'like', "%{$search}%")
                  ->orWhere('tramo_direccion', 'like', "%{$search}%");
            });
        }

        if (isset($this->filters['estado'])) {
            $query->where('estado', $this->filters['estado']);
        }

        if (isset($this->filters['localidad'])) {
            $query->where('localidad', $this->filters['localidad']);
        }

        if (isset($this->filters['id_contrato'])) {
            $query->where('id_contrato', $this->filters['id_contrato']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tipo Elemento CIV/RUPI',
            'Código Elemento',
            'Uso',
            'Área Elemento',
            'Localidad',
            'UPL',
            'Barrio',
            'Tramo/Dirección',
            'Eje',
            'Inicio',
            'Fin',
            'Reserva',
            'Estado',
            'ID Contrato',
            'Latitud',
            'Longitud',
            'Fecha Creación',
            'Fecha Actualización',
            'Fecha Eliminación',
        ];
    }

    public function map($proyecto): array
    {
        return [
            $proyecto->id,
            $proyecto->tipo_elemento_civ_rupi ?? '',
            $proyecto->codigo_elemento,
            $proyecto->uso ?? '',
            $proyecto->area_elemento ?? '',
            $proyecto->localidad ?? '',
            $proyecto->upl ?? '',
            $proyecto->barrio ?? '',
            $proyecto->tramo_direccion ?? '',
            $proyecto->eje ?? '',
            $proyecto->inicio ?? '',
            $proyecto->fin ?? '',
            $proyecto->reserva ?? '',
            $proyecto->estado ?? '',
            $proyecto->id_contrato ?? '',
            $proyecto->latitude ?? '',
            $proyecto->longitude ?? '',
            $proyecto->created_at->format('Y-m-d H:i:s'),
            $proyecto->updated_at->format('Y-m-d H:i:s'),
            $proyecto->deleted_at ? $proyecto->deleted_at->format('Y-m-d H:i:s') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}
