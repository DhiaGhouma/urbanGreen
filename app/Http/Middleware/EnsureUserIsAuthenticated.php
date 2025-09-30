<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Store intended URL for redirect after login
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            return redirect()->route('auth.login')
                ->with('warning', 'Please log in to access this page.');
        }

        // Check if user account is locked
        $user = Auth::user();
        if ($user->isLocked()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            $lockTime = $user->locked_until->diffForHumans();
            return redirect()->route('auth.login')
                ->with('error', "Your account is locked until {$lockTime}.");
        }

        return $next($request);
    }
}
