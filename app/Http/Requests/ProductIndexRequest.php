<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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

            'search' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'min_price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'max_price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'sort' => [
                'sometimes',
                'string',
                'in:newest,oldest,price_asc,price_desc,name_asc,name_desc',
            ],
        ];
    }
}