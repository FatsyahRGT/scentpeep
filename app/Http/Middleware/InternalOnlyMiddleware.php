<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalOnlyMiddleware
{
    /**
     * Handle an incoming request.
     * Ensures only internal management (Admin & Staff) can access sensitive operational routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isReseller()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Fitur ini khusus manajemen internal.'], 403);
            }

            return redirect()->route('stocks.reseller')
                ->with('error', 'Akses ditolak. Fitur ini khusus manajemen internal.');
        }

        return $next($request);
    }
}
