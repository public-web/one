<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class PreviabilizacionSocialController extends Controller
{
    /**
     * Redirect to banco proyectos index.
     */
    public function dashboard(): RedirectResponse
    {
        return redirect()->route('banco-proyectos.index');
    }
}
