<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function index()
    {
        $articulos = Articulo::with('user')->latest()->get();
        return response()->json($articulos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);

        $articulo = auth()->user()->articulos()->create($validated);

        return response()->json($articulo, 201);
    }

    public function update(Request $request, Articulo $articulo)
    {
        // Verificar que el usuario sea el dueño
        if ($articulo->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);

        $articulo->update($validated);

        return response()->json($articulo);
    }

    public function destroy(Articulo $articulo)
    {
        if ($articulo->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $articulo->delete();

        return response()->json(null, 204);
    }
}
