<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
        return [
            'method'  => ['required', 'string', 'in:card,konbini'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }
    public function attributes(): array
    {
        return [
            'method'  => '支払い方法',
            'address' => '配送先住所',
        ];
    }
    public function messages(): array
    {
        return [
            'method.required'  => '支払い方法は必須です。',
            'method.in'       => '支払い方法は「カード」または「コンビニ」のいずれかを選択してください。',
            'address.required' => '配送先住所は必須です。',
            'address.max'      => '配送先住所は255文字以内で入力してください。',
        ];
}
}