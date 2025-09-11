<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $conditions = ['新品・未使用','未使用に近い','目立った傷や汚れなし','やや傷や汚れあり','状態が悪い'];

        return [
            // 画像：必須・拡張子・最大４MB
            'image'       => ['nullable','image','mimes:jpeg,png,jpg','max:4096'],
            'title'       => ['required','string','max:255'],
            'brand'       => ['nullable','string','max:255'],
            'description' => ['required','string','max:2000'],
            'category_id.*' => ['integer','exists:categories,id'],
            'category_id'   => ['required', 'array', 'min:1'],
            'condition'   => ['required','string',\Illuminate\Validation\Rule::in($conditions)],
            'price'       => ['required','integer','min:1'], // 価格は1以上

        ];
    }
}
