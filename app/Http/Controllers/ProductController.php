<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        // Base query
        $query = Product::query();
        //SELECT * FROM products WHERE deleted_at IS NULL

        //filter by name (search)
        if($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        //filter by kategori (category_id)
        if($request->has('category_id')) {
            $query->where('category_id', '=', $request->category_id);
        }

        //filter by price range
        if($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // //sorting by rating
        // $sortBy = $request->get('sort_by', 'rating');
        // $query->orderBy($sortBy, $sortOrder);

        // //sorting by price
        // $sortBy = $request->get('sort_by', 'price');
        // $query->orderBy($sortBy, $sortOrder);

        // //sorting by download_count
        // $sortBy = $request->get('sort_by', 'download_count');
        // $query->orderBy($sortBy, $sortOrder);

        $sortBy = $request->sort_by;
        $sortOrder = $request->order === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['rating', 'price', 'download_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            // default sorting
            $query->orderBy('rating', 'desc');
        }

        // CACHE KEY
        // key dibuat unik berdasarkan query param (search, filter, sort diatas)
        $cacheKey = 'products_' . md5(json_encode($request->all()));

        // AMBIL DATA DARI CACHE ATAU DATABASE
        $products = Cache::remember($cacheKey, 60, function () use ($query) {
            // eager loading untuk menghindari N+1 query
            return $query->with(['category', 'seller'])->get();
        });

        // TRANSFORM DATA (map untuk format response)
        $products = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'title' => $p->title,
                'description' => $p->description,
                'price' => $p->price,
                'rating' => (float) $p->rating,
                'thumbnail' => $p->thumbnail,
                'file_path' => $p->file_path,
                'download_count' => $p->download_count,
                'status' => $p->status,
                'category' => [
                    'id' => $p->category->id,
                    'name' => $p->category->name,
                ],
                'seller' => [
                    'id' => $p->seller->id,
                    'name' => $p->seller->name,
                ],
                'rating_class' => $p->rating_class,
            ];
        });

        // RESPONSE
        return $this->successResponse($products,'Data produk berhasil diambil');
    }

    public function show(Product $product): JsonResponse {
        $mappedProduct = [
            'id' => $product->id,
            'title' => $product->title,
            'description' => $product->description,
            'price' => $product->price,
            'rating' => (float) $product->rating,
            'thumbnail' => $product->thumbnail,
            'file_path' => $product->file_path,
            'download_count' => $product->download_count,
            'status' => $product->status,
            'category' => [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ],
            'seller' => [
                'id' => $product->seller->id,
                'name' => $product->seller->name,
            ],
            'rating_class' => $product->rating_class
        ];

        return $this->successResponse($mappedProduct, 'Detail produk berhasil diambil');
    }

    public function store(Request $request): JsonResponse
    {
        // 1. validasi input
        $validated = $request->validate([
            // 'seller_id' => 'required|exists:users,id', // sudah pakai middleware -> EnsureSeller.php
            'category_id' => 'required|exists:product_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'rating' => 'required|numeric|min:0|max:10',
            'file_path' => 'required|string',
            'thumbnail' => 'nullable|string',
            'status' => 'in:active,inactive'
        ], [
            // 'seller_id.required' => 'Id seller wajib diisi', // sudah pakai middleware -> EnsureSeller.php
            'category_id.required' => 'Id kategori produk wajib diisi',
            'title.required' => 'Title produk wajib diisi',
            'description.required' => 'Deskripsi produk wajib diisi',
            'price.required' => 'Harga produk wajib diisi',
            'rating.required' => 'Rating produk wajib diisi',
            'file_path.required' => 'Path file produk wajib diisi',
        ]);

        // sudah pakai middleware -> EnsureSeller.php
        // 2. Validasi user
        // $user = User::findOrFail($validated['seller_id']);
        // if ($user->role !== 'seller') {
        //     //Forbidden -> tidak punya hak akses
        //     return $this->errorResponse('Hanya seller yang boleh menambahkan produk', 403);
        // }

        // 3. simpan data ke database
        // $category = Product::create($validated);
        $product = Product::create([
            ...$validated,
            'seller_id' => $request->user()->id
        ]);

        Cache::flush(); // hapus semua cache products

        // 4. return response JSON
        return $this->successResponse($product, 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        // 1. validasi input
        $validated = $request->validate([
            // 'seller_id' => 'required|exists:users,id', // sudah pakai middleware -> EnsureSeller.php
            'category_id' => 'required|exists:product_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'rating' => 'required|numeric|min:0|max:10',
            'file_path' => 'required|string',
            'thumbnail' => 'nullable|string',
            'status' => 'in:active,inactive'
        ], [
            // 'seller_id.required' => 'Id seller wajib diisi', // sudah pakai middleware -> EnsureSeller.php
            'category_id.required' => 'Id kategori produk wajib diisi',
            'title.required' => 'Title produk wajib diisi',
            'description.required' => 'Deskripsi produk wajib diisi',
            'price.required' => 'Harga produk wajib diisi',
            'rating.required' => 'Rating produk wajib diisi',
            'file_path.required' => 'Path file produk wajib diisi',
        ]);

        // sudah pakai middleware -> EnsureSeller.php
        // 2. Validasi user
        // $user = User::findOrFail($validated['seller_id']);
        // if ($user->role !== 'seller') {
        //     //Forbidden -> tidak punya hak akses
        //     return $this->errorResponse('Hanya seller yang boleh update produk', 403);
        // }

         // sudah pakai middleware -> EnsureProductOwner.php
        // 2. cari & update data
        // $product = Product::findOrFail($id);
        // if ($product->seller_id !== $request->user()->id) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Anda tidak memiliki akses untuk mengubah data ini'
        //     ], 403);
        // }
        $product->update($validated);

        Cache::flush(); // reset cache karena data berubah

        // 3. return response JSON
        return $this->successResponse($product, 'Produk berhasil diupdate');
    }

    public function destroy(Product $product): JsonResponse
    {
        // digantikan oleh Route Model Binding
        // 1. cari data
        // $product = Product::findOrFail($id);
        $productName = $product->title;

        // sudah pakai middleware -> EnsureSeller.php
        // 2. Validasi user
        // $user = User::findOrFail(2); // seller A

        // if ($user->role !== 'seller') {
        //     //Forbidden -> tidak punya hak akses
        //     return $this->errorResponse('Hanya seller yang boleh delete produk', 403);
        // }

        // 3. error handling, delete data & return response JSON
        try {
            $product->delete();
            Cache::flush(); // reset cache
        } catch (\Exception $e) {
            //Conflict -> konflik dengan state data
            return $this->errorResponse('Gagal menghapus produk', 409);
        }
        return $this->successResponse(null, "Produk '$productName' berhasil dihapus");
    }

    public function productCountPerSeller(): JsonResponse
    {
        // 1. Ambil hanya user dengan role seller
        $users = User::where('role', 'seller')
            ->withCount('products') // hitung jumlah produk per seller
            ->get();
        /**
         * SELECT users.*,
         * (
         *      SELECT COUNT(*)
         *      FROM products
         *      WHERE products.seller_id = users.id
         *      AND products.deleted_at IS NULL
         * ) as products_count
         * FROM users
         * WHERE role = 'seller';
         */

        // 2. Format response
        $data = $users->map(function ($user) {
            return [
                'seller_id' => $user->id,
                'seller_name' => $user->name,
                'total_products' => $user->products_count
            ];
        });

        // 3. Return response
        return $this->successResponse($data, 'Jumlah produk per seller');
    }

    public function transactionDetail(): JsonResponse
    {
        $data = DB::table('order_items')

            // join ke orders
            ->join('orders', 'order_items.order_id', '=', 'orders.id')

            // join ke users (buyer)
            ->join('users', 'orders.user_id', '=', 'users.id')

            // join ke products
            ->join('products', 'order_items.product_id', '=', 'products.id')

            // join ke categories
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')

            ->select(
                'orders.id as order_id',
                'orders.invoice_number',
                'users.name as buyer_name',

                'products.title as product_title',
                'product_categories.name as category_name',

                'order_items.quantity',
                'order_items.price_at_purchase',

                'orders.total_price',
                'orders.status',
                'orders.created_at'
            )
            ->get();
            /**
             * SELECT
             *      orders.id AS order_id,
             *      orders.invoice_number,
             *      users.name AS buyer_name,
             *      products.title AS product_title,
             *      product_categories.name AS category_name,
             *      order_items.quantity,
             *      order_items.price_at_purchase,
             *      orders.total_price,
             *      orders.status,
             *      orders.created_at
             * FROM order_items
             * JOIN orders
             *      ON order_items.order_id = orders.id
             * JOIN users
             *      ON orders.user_id = users.id
             * JOIN products
             *      ON order_items.product_id = products.id
             * JOIN product_categories
             *      ON products.category_id = product_categories.id
             */

        return $this->successResponse($data, 'Detail transaksi berhasil diambil');
    }
}
