<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Product;

class EnsureProductOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // ambil dari route model binding
        $product = $request->route('product');

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukaNn'
            ], 404);
        }

        if ($product->seller_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengakses produk ini'
            ], 403);
        }

        return $next($request);
    }
}
