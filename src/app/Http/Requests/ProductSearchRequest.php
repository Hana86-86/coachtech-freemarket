<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductSearchRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'keyword'      => ['nullable','string','max:100'],
            'categories'   => ['nullable','array'],
            'categories.*' => ['integer','exists:categories,id'],
            'price_min'    => ['nullable','integer','min:0'],
            'price_max'    => ['nullable','integer','min:0'],
            'conditions'   => ['nullable','array'],
            'conditions.*' => ['string','in:新品,未使用に近い,目立った傷や汚れなし,やや傷や汚れあり,状態が悪い'],
        ];
    }
    public function validated($key = null, $default = null)
    {
        return array_filter(parent::validated(), static function ($v) {
            return !($v === null || $v === '' || $v === []);
        });
    }
}
