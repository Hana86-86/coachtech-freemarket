<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
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
            'image'       => ['required','image','mimes:jpeg,png,jpg','max:4096'],
            'title'       => ['required','string','max:255'],
            'brand'       => ['nullable','string','max:255'],
            'description' => ['required','string','max:2000'],
            'category_id.*' => ['integer','exists:categories,id'],
            'category_id'   => ['required', 'array', 'min:1'],
            'condition'   => ['required','string',Rule::in($conditions)],
            'price'       => ['required','integer','min:1'], // 価格は1以上

        ];
    }

    public function attributes(): array
    {
        return [
            'image'       => '商品画像',
            'category_id' => 'カテゴリー',
            'condition'   => '商品の状態',
            'title'       => '商品名',
            'brand'       => 'ブランド名',
            'description' => '商品説明',
            'price'       => '価格',
        ];
}
    public function messages(): array
    {
        return [
            'image.required'       => ':attribute は必須です。',
            'image.image'          => ':attribute は画像ファイルを選択してください。',
            'image.mimes'          => ':attribute は JPEG または PNG を選択してください。',
            'image.max'            => ':attribute は画像は4MB以下にしてください。',

            'title.required'      => ':attribute は必須です。',
            'title.string'        => ':attribute は文字列で入力してください。',
            'title.max'           => ':attribute は255文字以内で入力してください。',

            'brand.string'       => ':attribute は文字列で入力してください。',
            'brand.max'          => ':attribute は255文字以内で入力してください。',

            'description.required' => ':attribute は必須です。',
            'description.string'   => ':attribute は文字列で入力してください。',
            'description.max'      => ':attribute は2000文字以内で入力してください。',

            'category_id.required' => ':attribute は必須です。',
            'category_id.exists'   => '選択した :attribute は存在しません。',

            'condition.required'   => ':attribute を選択してください。',
            'condition.string'     => ':attribute の形式が不正です。',
            'condition.in'         => '選択した :attribute が不正です。',

            'price.required'       => ':attribute は必須です。',
            'price.integer'        => ':attribute は整数で入力してください。',
            'price.min'            => '価格は1以上である必要があります。',
        ];
    }
}