<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;
use App\Http\Requests\StoreProductCategoryRequest;

class ProductCategoryController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $categories = ProductCategory::all();

        return $this->successResponse($categories, "Data kategori berhasil diambil");
    }

    public function show(ProductCategory $category): JsonResponse {
        return $this->successResponse($category, 'Detail kategori berhasil diambil');
    }

    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        // 1. ambil data valid
        $validated = $request->validated();

        // 2. simpan ke database
        $category = ProductCategory::create($validated);

        // 3. return response
        return $this->successResponse($category, 'Kategori berhasil ditambahkan', 201);
    }

    public function update(Request $request, ProductCategory $category): JsonResponse
    {
        // 1. validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:product_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string'
        ], [
            'name.required' => 'Nama kategori wajib diisi',
        ]);

        // digantikan oleh Route Model Binding
        // 2. cari & update data
        // $category = ProductCategory::findOrFail($id);
        $category->update($validated);

        // 3. return response JSON
        return $this->successResponse($category, 'Kategori berhasil diupdate');
    }

    public function destroy(ProductCategory $category): JsonResponse
    {
        // digantikan oleh Route Model Binding
        // 1. cari & hapus data
        // $category = ProductCategory::findOrFail($id);
        $categoryName = $category->name;

        // 2. return response JSON
        if ($category->products()->exists()) {
            return $this->errorResponse("Kategori '$categoryName' tidak bisa dihapus karena masih memiliki produk");
        }

        try {
            $category->delete();
        } catch (\Exception $e) {
            //Conflict -> konflik dengan state data
            return $this->errorResponse('Gagal menghapus kategori', 409);
        }
        return $this->successResponse(null, "Kategori '$categoryName' berhasil dihapus");
    }
}
