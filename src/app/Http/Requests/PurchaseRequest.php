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
            'payment_method'  => ['required', 'string', 'in:card,konbini'],
        ];
    }
    public function attributes(): array
    {
        return [
            'payment_method'  => '支払い方法',
        ];
    }
    public function messages(): array
    {
        return [
            'payment_method.required'  => '支払い方法は必須です。',
            'payment_method.in'       => '支払い方法は「カード」または「コンビニ」のいずれかを選択してください。',
        ];
}
}