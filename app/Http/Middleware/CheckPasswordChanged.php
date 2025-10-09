<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated and needs to change password
        if ($user && $user->needsPasswordChange()) {
            // Skip middleware for password change routes and logout
            $allowedRoutes = [
                'password.change.show',
                'password.change.update',
                'logout',
            ];

            if (! in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('password.change.show');
            }
        }

        return $next($request);
    }
}
