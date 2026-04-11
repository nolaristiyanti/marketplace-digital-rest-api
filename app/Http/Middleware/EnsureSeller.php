<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'seller') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya seller yang boleh mengakses fitur ini'
            ], 403);
        }

        return $next($request);
    }
}
