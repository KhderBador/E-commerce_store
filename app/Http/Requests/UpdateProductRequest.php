<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'short_description' => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'stock' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'sku' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],

            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product->id),
            ],

            'status' => [
                'sometimes',
                'string',
                'in:active,inactive',
            ],

            'category_id' => [
                'sometimes',
                'integer',
                'exists:categories,id',
            ],

            'brand_id' => [
                'sometimes',
                'integer',
                'exists:brands,id',
            ],
        ];
    }
}