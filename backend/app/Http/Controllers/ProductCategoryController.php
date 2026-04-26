<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;

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

    public function update(UpdateProductCategoryRequest  $request, ProductCategory $category): JsonResponse
    {
        // 1. update data (hanya field yang dikirim)
        $category->update($request->validated());

        // 2. return response
        return $this->successResponse($category, 'Kategori berhasil diupdate', 200);
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
