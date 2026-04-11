<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        //get data dengan filtering & sorting
        $query = Product::query();

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

        $sortOrder = $request->order === 'asc' ? 'asc' : 'desc';

        //sorting by rating
        $sortBy = $request->get('sort_by', 'rating');
        $query->orderBy($sortBy, $sortOrder);

        //sorting by price
        $sortBy = $request->get('sort_by', 'price');
        $query->orderBy($sortBy, $sortOrder);

        //sorting by download_count
        $sortBy = $request->get('sort_by', 'download_count');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->get()->map(fn ($p) => [
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
        ]);

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

        // 4. return response JSON
        return $this->successResponse($product, 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        // 1. validasi input
        $validated = $request->validate([
            'seller_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:product_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'rating' => 'required|numeric|min:0|max:10',
            'category_id' => 'required|exists:product_categories,id',
            'file_path' => 'required|string',
            'thumbnail' => 'nullable|string',
            'status' => 'in:active,inactive'
        ], [
            'seller_id.required' => 'Id seller wajib diisi',
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

        // 2. cari & update data
        $product = Product::findOrFail($id);
        $product->update($validated);

        // 3. return response JSON
        return $this->successResponse($product, 'Produk berhasil diupdate');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        // 1. cari data
        $product = Product::findOrFail($id);
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
        } catch (\Exception $e) {
            //Conflict -> konflik dengan state data
            return $this->errorResponse('Gagal menghapus produk', 409);
        }
        return $this->successResponse(null, "Produk '$productName' berhasil dihapus");
    }
}
