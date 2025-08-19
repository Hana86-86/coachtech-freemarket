<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name'          => ['required', 'string', 'max:20'],
            // 15MB, 画像のみ, 4000px 超は不可
            'profile_image' => ['nullable','image','mimes:jpeg,jpg,png,webp','max:15360','dimensions:max_width=4000,max_height=4000'],
            'postal_code'   => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], // 123-4567形式のみ許可
            'address'       => ['required', 'string', 'max:255'],
            'building'      => ['nullable', 'string', 'max:255'],
        ];
    }
    public function attributes(): array
    {
        return [
            'name'        => '名前',
            'profile_image' => 'プロフィール画像',
            'postal_code' => '郵便番号',
            'address'     => '住所',
            'building'    => '建物名',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required'        => ':attribute は必須です。',
            'name.max'             => ':attribute は :max文字以内で入力してください。',
            'postal_code.required' => ':attribute は必須です。',
            'postal_code.regex'    => ':attribute は「123-4567」の形式で入力してください。',
            'address.required'     => ':attribute は必須です。',
            'profile_image.image'  => ':attribute は画像ファイルを選択してください。',
            'profile_image.mimes'  => ':attribute は jpeg / png / jpg のいずれかを選択してください。',
            'profile_image.max'    => ':attribute は 2MB 以下のファイルを選択してください。 ',
        ];

}
}