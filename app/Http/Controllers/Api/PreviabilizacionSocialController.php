<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreviabilizacionSocial;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PreviabilizacionSocialController extends Controller
{
    /**
     * Get statistics for the dashboard
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total' => PreviabilizacionSocial::count(),
                'por_priorizado' => PreviabilizacionSocial::select('priorizado_por', DB::raw('count(*) as total'))
                    ->whereNotNull('priorizado_por')
                    ->where('priorizado_por', '!=', '')
                    ->groupBy('priorizado_por')
                    ->orderBy('total', 'desc')
                    ->get(),
                'por_tipo' => PreviabilizacionSocial::select('tipo_previabilizacion', DB::raw('count(*) as total'))
                    ->whereNotNull('tipo_previabilizacion')
                    ->where('tipo_previabilizacion', '!=', '')
                    ->groupBy('tipo_previabilizacion')
                    ->orderBy('total', 'desc')
                    ->get(),
                'por_mes' => PreviabilizacionSocial::select(
                        DB::raw('DATE_FORMAT(fecha, "%Y-%m") as mes'),
                        DB::raw('count(*) as total')
                    )
                    ->whereNotNull('fecha')
                    ->groupBy('mes')
                    ->orderBy('mes', 'desc')
                    ->limit(12)
                    ->get(),
                'recientes' => PreviabilizacionSocial::orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
