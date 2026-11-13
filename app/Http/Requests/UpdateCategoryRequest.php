<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],

            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($category->id),
            ],

            'description' => ['sometimes', 'nullable', 'string'],

            'parent_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'is_active' => ['sometimes', 'boolean'],

            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}