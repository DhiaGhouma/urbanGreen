<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Check if user has admin role
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Accès refusé. Vous devez être administrateur pour accéder à cette page.');
        }

        return $next($request);
    }
}
