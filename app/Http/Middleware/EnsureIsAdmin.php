<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for server-to-server Admin API key
        $apiKey = $request->header('X-Admin-Api-Key');
        $validKey = env('SECRET_KEY', 'dev-wastewise-flask-secret-key-2026');

        if ($apiKey && hash_equals($validKey, $apiKey)) {
            return $next($request);
        }

        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
            return redirect()->route('login');
        }

        if (!Auth::user()->isAdmin()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Administrator privileges required.',
                ], 403);
            }
            abort(403, 'Unauthorized access. Administrator privileges required.');
        }

        return $next($request);
    }
}
