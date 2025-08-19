<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'postal_code' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], // 123-4567形式のみ許可
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
        ];
    }
    public function attributes(): array
    {
        return [
            'postal_code' => '郵便番号',
            'address'     => '住所',
            'building'    => '建物名',
        ];
    }
    public function messages(): array
    {
        return [
            'postal_code.required' => '郵便番号は必須です。',
            'postal_code.regex'    => '郵便番号は「123-4567」の形式で入力してください。',
            'address.required'     => '住所は必須です。',
        ];
    }

}