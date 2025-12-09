<?php

namespace App\Http\Controllers\Api;

use App\Exports\BancoProyectosExport;
use App\Http\Controllers\Controller;
use App\Imports\BancoProyectosImport;
use App\Models\BancoProyecto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BancoProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', BancoProyecto::class);

        $query = BancoProyecto::query();

        // Búsqueda
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->byEstado($request->estado);
        }

        // Filtro por localidad
        if ($request->filled('localidad')) {
            $query->byLocalidad($request->localidad);
        }

        // Filtro por contrato
        if ($request->filled('id_contrato')) {
            $query->byContrato($request->id_contrato);
        }

        // Incluir eliminados si se solicita
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        // Ordenamiento
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $perPage = $request->input('per_page', 15);
        $proyectos = $query->paginate($perPage);

        return response()->json($proyectos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', BancoProyecto::class);

        $validator = Validator::make($request->all(), [
            'tipo_elemento_civ_rupi' => 'nullable|string|max:255',
            'codigo_elemento' => 'required|string|max:255|unique:banco_proyectos,codigo_elemento',
            'uso' => 'nullable|string|max:255',
            'area_elemento' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:255',
            'upl' => 'nullable|string|max:255',
            'barrio' => 'nullable|string|max:255',
            'tramo_direccion' => 'nullable|string',
            'eje' => 'nullable|string|max:255',
            'inicio' => 'nullable|string|max:255',
            'fin' => 'nullable|string|max:255',
            'reserva' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'id_contrato' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $proyecto = BancoProyecto::create($validator->validated());

            return response()->json([
                'message' => 'Proyecto creado exitosamente',
                'data' => $proyecto
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $proyecto = BancoProyecto::withTrashed()->findOrFail($id);

        $this->authorize('view', $proyecto);

        return response()->json([
            'data' => $proyecto
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $proyecto = BancoProyecto::findOrFail($id);

        $this->authorize('update', $proyecto);

        $validator = Validator::make($request->all(), [
            'tipo_elemento_civ_rupi' => 'nullable|string|max:255',
            'codigo_elemento' => 'required|string|max:255|unique:banco_proyectos,codigo_elemento,' . $id,
            'uso' => 'nullable|string|max:255',
            'area_elemento' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:255',
            'upl' => 'nullable|string|max:255',
            'barrio' => 'nullable|string|max:255',
            'tramo_direccion' => 'nullable|string',
            'eje' => 'nullable|string|max:255',
            'inicio' => 'nullable|string|max:255',
            'fin' => 'nullable|string|max:255',
            'reserva' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'id_contrato' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $proyecto->update($validator->validated());

            return response()->json([
                'message' => 'Proyecto actualizado exitosamente',
                'data' => $proyecto->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $proyecto = BancoProyecto::findOrFail($id);

        $this->authorize('delete', $proyecto);

        try {
            $proyecto->delete();

            return response()->json([
                'message' => 'Proyecto eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a soft deleted resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $proyecto = BancoProyecto::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $proyecto);

        try {
            $proyecto->restore();

            return response()->json([
                'message' => 'Proyecto restaurado exitosamente',
                'data' => $proyecto->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al restaurar el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete a resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        $proyecto = BancoProyecto::onlyTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $proyecto);

        try {
            $proyecto->forceDelete();

            return response()->json([
                'message' => 'Proyecto eliminado permanentemente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar permanentemente el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for the dashboard
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $this->authorize('viewAny', BancoProyecto::class);

        try {
            $stats = [
                'total' => BancoProyecto::count(),
                'por_localidad' => BancoProyecto::select('localidad', DB::raw('count(*) as total'))
                    ->groupBy('localidad')
                    ->get(),
                'por_estado' => BancoProyecto::select('estado', DB::raw('count(*) as total'))
                    ->groupBy('estado')
                    ->get(),
                'por_contrato' => BancoProyecto::select('id_contrato', DB::raw('count(*) as total'))
                    ->whereNotNull('id_contrato')
                    ->where('id_contrato', '!=', '')
                    ->groupBy('id_contrato')
                    ->orderBy('id_contrato')
                    ->get(),
                'eliminados' => BancoProyecto::onlyTrashed()->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export proyectos to Excel or CSV
     *
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function export(Request $request): BinaryFileResponse
    {
        $this->authorize('viewAny', BancoProyecto::class);

        $filters = $request->only(['search', 'estado', 'localidad', 'id_contrato']);
        $format = $request->input('format', 'xlsx'); // xlsx or csv

        $export = new BancoProyectosExport($filters);
        $fileName = 'banco_proyectos_' . now()->format('Y-m-d_His') . '.' . $format;

        if ($format === 'csv') {
            return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Download import template
     *
     * @return BinaryFileResponse
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $this->authorize('create', BancoProyecto::class);

        // Create a simple export with headers only
        $export = new class implements \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\FromCollection {
            public function collection()
            {
                // Return a single example row to guide users
                return collect([[
                    'CIV',
                    'EJEMPLO-001',
                    'Ejemplo de uso',
                    '100',
                    'Ejemplo Localidad',
                    'UPL-01',
                    'Ejemplo Barrio',
                    'Calle Ejemplo #123',
                    'Eje Principal',
                    'Punto A',
                    'Punto B',
                    'Reserva ejemplo',
                    'Activo',
                    'CONT-001',
                    '4.60971',
                    '-74.07765',
                ]]);
            }

            public function headings(): array
            {
                return [
                    'Tipo Elemento (CIV o RUPI)',
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
                ];
            }

            public function map($row): array
            {
                return $row;
            }
        };

        $fileName = 'plantilla_banco_proyectos.xlsx';
        return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Import proyectos from Excel
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        $this->authorize('create', BancoProyecto::class);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $import = new BancoProyectosImport();

            Excel::import($import, $file);

            $successCount = $import->getSuccessCount();
            $failures = $import->getFailures();
            $errors = $import->getErrors();

            $response = [
                'message' => "Importación completada. {$successCount} proyecto(s) importado(s).",
                'success_count' => $successCount,
                'errors_count' => count($failures) + count($errors),
            ];

            if (count($failures) > 0 || count($errors) > 0) {
                $response['failures'] = array_merge(
                    array_map(function ($failure) {
                        return [
                            'row' => $failure->row(),
                            'attribute' => $failure->attribute(),
                            'errors' => $failure->errors(),
                            'values' => $failure->values(),
                        ];
                    }, $failures),
                    $errors
                );
            }

            return response()->json($response, count($failures) > 0 || count($errors) > 0 ? 422 : 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al importar el archivo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
