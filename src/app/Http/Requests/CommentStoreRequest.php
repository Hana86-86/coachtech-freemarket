<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
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
            'body' => ['required', 'string', 'max:255']
        ];
    }
    public function attributes(): array
    {
        return [
            'body' => 'コメント',
        ];
    }
    public function messages(): array
    {
        return [
            'body.required' => 'コメントを入力してください。',
            'body.max'      => 'コメントは255文字以内で入力してください。',
        ];
    }
}
