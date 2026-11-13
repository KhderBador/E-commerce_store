<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
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
       $brand= $this->route('brand');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],

            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'slug')->ignore($brand->id),
            ],

            'description' => ['sometimes', 'nullable', 'string'],

            'logo'=>['sometimes','nullable','string'],
            'is_active' => ['sometimes', 'boolean'],

            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
