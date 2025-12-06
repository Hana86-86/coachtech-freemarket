<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTradeMessageRequest extends FormRequest
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
            'body' => ['required', 'string', 'max:400'],
            'image' => ['nullable', 'mimes:jpeg,png', 'max:4096'],
        ];
    }
    public function messages(): array
    {
        return [
            'body.required'  => '本文を入力してください。',
            'body.string'    => '本文を文字列で入力してください。',
            'body.max'       => '本文は400文字以内で入力してください。',

            'image.mimes'    => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.max'      => '画像サイズは4MB以下にしてください。',
        ];
    }
}
