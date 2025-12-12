<?php

namespace App\Http\Controllers;

use App\Models\BancoProyecto;
use App\Models\DetalleBancoProyecto;
use App\Models\DetalleDocumento;
use App\Models\PreviabilizacionSocial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BancoProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', BancoProyecto::class);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $canManageBancoProyectos = $user->hasAnyRole(['superadmin', 'banco-proyectos']);

        return Inertia::render('BancoProyectos/Index', [
            'canManageBancoProyectos' => $canManageBancoProyectos,
        ]);
    }

    /**
     * Display the map view.
     */
    public function map(): Response
    {
        $this->authorize('viewAny', BancoProyecto::class);

        return Inertia::render('BancoProyectos/Map');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): Response
    {
        $proyecto = BancoProyecto::withTrashed()
            ->with([
                'detalles' => function ($query) {
                    $query->with('documentos')->orderBy('created_at', 'desc');
                },
                'previabilizaciones' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])
            ->findOrFail($id);

        $this->authorize('view', $proyecto);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $canManageDetalles = $user->hasRole('banco-proyectos');
        $canManagePreviabilizaciones = $user->hasRole('previabilizacion-social');

        return Inertia::render('BancoProyectos/Show', [
            'proyecto' => $proyecto,
            'canManageDetalles' => $canManageDetalles,
            'canManagePreviabilizaciones' => $canManagePreviabilizaciones,
        ]);
    }

    /**
     * Store a new detalle for the proyecto.
     */
    public function storeDetalle(Request $request, int $proyectoId): RedirectResponse
    {
        $proyecto = BancoProyecto::findOrFail($proyectoId);
        $this->authorize('view', $proyecto);

        $validated = $request->validate([
            'numero_radicado_entrada' => 'nullable|string|max:255',
            'fecha_entrada' => 'nullable|date',
            'peticionario' => 'nullable|string|max:255',
            'asunto' => 'nullable|string',
            'numero_radicado_salida' => 'nullable|string|max:255',
            'fecha_salida' => 'nullable|date',
            'observacion' => 'nullable|string',
            'documentos' => 'nullable|array',
            'documentos.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        // Create detalle
        $detalle = DetalleBancoProyecto::create([
            'codigo' => $proyecto->codigo_elemento,
            'numero_radicado_entrada' => $validated['numero_radicado_entrada'] ?? null,
            'fecha_entrada' => $validated['fecha_entrada'] ?? null,
            'peticionario' => $validated['peticionario'] ?? null,
            'asunto' => $validated['asunto'] ?? null,
            'numero_radicado_salida' => $validated['numero_radicado_salida'] ?? null,
            'fecha_salida' => $validated['fecha_salida'] ?? null,
            'observacion' => $validated['observacion'] ?? null,
        ]);

        // Handle multiple file uploads
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('detalle_proyectos', $filename, 'public');

                DetalleDocumento::create([
                    'detalle_banco_proyecto_id' => $detalle->id,
                    'archivo_path' => $path,
                    'archivo_nombre' => $file->getClientOriginalName(),
                    'archivo_tipo' => $file->getClientMimeType(),
                    'archivo_tamanio' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('banco-proyectos.show', $proyectoId)
            ->with('success', 'Detalle creado correctamente');
    }

    /**
     * Update the specified detalle.
     */
    public function updateDetalle(Request $request, int $proyectoId, int $detalleId): RedirectResponse
    {
        $proyecto = BancoProyecto::findOrFail($proyectoId);
        $this->authorize('view', $proyecto);

        $detalle = DetalleBancoProyecto::where('id', $detalleId)
            ->where('codigo', $proyecto->codigo_elemento)
            ->firstOrFail();

        $validated = $request->validate([
            'numero_radicado_entrada' => 'nullable|string|max:255',
            'fecha_entrada' => 'nullable|date',
            'peticionario' => 'nullable|string|max:255',
            'asunto' => 'nullable|string',
            'numero_radicado_salida' => 'nullable|string|max:255',
            'fecha_salida' => 'nullable|date',
            'observacion' => 'nullable|string',
            'documentos' => 'nullable|array',
            'documentos.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        // Update detalle fields
        $detalle->update([
            'numero_radicado_entrada' => $validated['numero_radicado_entrada'] ?? null,
            'fecha_entrada' => $validated['fecha_entrada'] ?? null,
            'peticionario' => $validated['peticionario'] ?? null,
            'asunto' => $validated['asunto'] ?? null,
            'numero_radicado_salida' => $validated['numero_radicado_salida'] ?? null,
            'fecha_salida' => $validated['fecha_salida'] ?? null,
            'observacion' => $validated['observacion'] ?? null,
        ]);

        // Handle new file uploads (add to existing ones)
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('detalle_proyectos', $filename, 'public');

                DetalleDocumento::create([
                    'detalle_banco_proyecto_id' => $detalle->id,
                    'archivo_path' => $path,
                    'archivo_nombre' => $file->getClientOriginalName(),
                    'archivo_tipo' => $file->getClientMimeType(),
                    'archivo_tamanio' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('banco-proyectos.show', $proyectoId)
            ->with('success', 'Detalle actualizado correctamente');
    }

    /**
     * Remove the specified detalle.
     */
    public function destroyDetalle(int $proyectoId, int $detalleId): RedirectResponse
    {
        $proyecto = BancoProyecto::findOrFail($proyectoId);
        $this->authorize('view', $proyecto);

        $detalle = DetalleBancoProyecto::where('id', $detalleId)
            ->where('codigo', $proyecto->codigo_elemento)
            ->firstOrFail();

        // Delete all associated documents
        foreach ($detalle->documentos as $documento) {
            $documento->deleteArchivo();
            $documento->delete();
        }

        $detalle->delete();

        return redirect()->route('banco-proyectos.show', $proyectoId)
            ->with('success', 'Detalle eliminado correctamente');
    }

    /**
     * Delete an individual document from a detalle.
     */
    public function destroyDocumento(int $proyectoId, int $detalleId, int $documentoId): RedirectResponse
    {
        $proyecto = BancoProyecto::findOrFail($proyectoId);
        $this->authorize('view', $proyecto);

        $detalle = DetalleBancoProyecto::where('id', $detalleId)
            ->where('codigo', $proyecto->codigo_elemento)
            ->firstOrFail();

        $documento = DetalleDocumento::where('id', $documentoId)
            ->where('detalle_banco_proyecto_id', $detalle->id)
            ->firstOrFail();

        $documento->deleteArchivo();
        $documento->delete();

        return redirect()->route('banco-proyectos.show', $proyectoId)
            ->with('success', 'Documento eliminado correctamente');
    }

    /**
     * Store a new previabilizacion for the proyecto.
     */
    public function storePreviabilizacion(Request $request, int $proyectoId): RedirectResponse
    {
        $proyecto = BancoProyecto::findOrFail($proyectoId);
        $this->authorize('view', $proyecto);

        $validated = $request->validate([
            'fecha' => 'nullable|date',
            'priorizado_por' => 'nullable|string|max:255',
            'tipo_previabilizacion' => 'nullable|string|max:255',
        ]);

        PreviabilizacionSocial::create([
            'codigo' => $proyecto->codigo_elemento,
            'fecha' => $validated['fecha'] ?? null,
            'priorizado_por' => $validated['priorizado_por'] ?? null,
            'tipo_previabilizacion' => $validated['tipo_previabilizacion'] ?? null,
        ]);

        return redirect()->route('banco-proyectos.show', $proyectoId)
            ->with('success', 'Previabilización creada correctamente');
    }

    /**
     * Update the specified previabilizacion.
     */
    public function updatePreviabilizacion(Request $request, int $proyectoId, int $previabilizacionId): RedirectResponse
    {
        $proyecto = BancoProyecto::findOrFail($proyectoId);
        $this->authorize('view', $proyecto);

        $previabilizacion = PreviabilizacionSocial::where('id', $previabilizacionId)
            ->where('codigo', $proyecto->codigo_elemento)
            ->firstOrFail();

        $validated = $request->validate([
            'fecha' => 'nullable|date',
            'priorizado_por' => 'nullable|string|max:255',
            'tipo_previabilizacion' => 'nullable|string|max:255',
        ]);

        $previabilizacion->update([
            'fecha' => $validated['fecha'] ?? null,
            'priorizado_por' => $validated['priorizado_por'] ?? null,
            'tipo_previabilizacion' => $validated['tipo_previabilizacion'] ?? null,
        ]);

        return redirect()->route('banco-proyectos.show', $proyectoId)
            ->with('success', 'Previabilización actualizada correctamente');
    }

    /**
     * Remove the specified previabilizacion.
     */
    public function destroyPreviabilizacion(int $proyectoId, int $previabilizacionId): RedirectResponse
    {
        $proyecto = BancoProyecto::findOrFail($proyectoId);
        $this->authorize('view', $proyecto);

        $previabilizacion = PreviabilizacionSocial::where('id', $previabilizacionId)
            ->where('codigo', $proyecto->codigo_elemento)
            ->firstOrFail();

        $previabilizacion->delete();

        return redirect()->route('banco-proyectos.show', $proyectoId)
            ->with('success', 'Previabilización eliminada correctamente');
    }
}
