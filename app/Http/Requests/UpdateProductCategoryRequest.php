<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductCategoryRequest extends FormRequest
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
        $categoryId = $this->route('category')->id;

        return [
            'name' => 'sometimes|required|string|max:100|unique:product_categories,name,' . $categoryId,
            'description' => 'sometimes|nullable|string|max:500',
            'icon' => 'sometimes|nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            // name
            'name.required' => 'Nama kategori wajib diisi',
            'name.max' => 'Nama kategori maksimal 100 karakter',
            'name.unique' => 'Nama kategori sudah digunakan',

            // description
            'description.max' => 'Deskripsi maksimal 500 karakter',

            // icon
            'icon.max' => 'Icon maksimal 255 karakter',
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
