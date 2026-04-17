<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //sudah pakai middleware EnsureSeller, jadi authorization sudah ditangani di sana
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|required|integer|exists:product_categories,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'rating' => 'sometimes|required|numeric|min:0|max:10',
            'file_path' => 'sometimes|required|string|max:255',
            'thumbnail' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|nullable|string|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            // category_id
            'category_id.required' => 'Id kategori produk wajib diisi',
            'category_id.exists' => 'Kategori tidak ditemukan',

            // title
            'title.required' => 'Title produk wajib diisi',
            'title.max' => 'Title maksimal 255 karakter',

            // description
            'description.required' => 'Deskripsi produk wajib diisi',
            'description.string' => 'Deskripsi harus berupa teks',

            // price
            'price.required' => 'Harga produk wajib diisi',
            'price.numeric' => 'Harga harus berupa angka', // untuk belajar : ini akan di handle di exception bootstrap/app.php
            'price.min' => 'Harga tidak boleh kurang dari 0',

            // rating
            'rating.required' => 'Rating produk wajib diisi',
            'rating.min' => 'Rating minimal 0',
            'rating.max' => 'Rating maksimal 10',

            // file_path
            'file_path.required' => 'Path file produk wajib diisi',
            'file_path.max' => 'Path file maksimal 255 karakter',

            // thumbnail
            'thumbnail.max' => 'Thumbnail maksimal 255 karakter',

            // status
            'status.in' => 'Status harus bernilai active atau inactive',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // default Laravel saat validasi gagal. custom format response error
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validasi gagal',
            'errors' => $validator->errors(),
        ], 400));
    }
}
