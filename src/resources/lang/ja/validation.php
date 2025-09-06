<?php

return [
    'required'  => ':attribute は必須です。',
    'string'    => ':attribute は文字列で入力してください。',
    'integer'   => ':attribute は整数で入力してください。',
    'numeric'   => ':attribute は数値で入力してください。',
    'email'     => ':attribute はメール形式で入力してください。',
    'min'       => [
        'string' => ':attribute は :min 文字以上で入力してください。',
        'numeric'=> ':attribute は :min 以上で入力してください。',
        'array'  => ':attribute は :min 個以上選択してください。',
    ],
    'max'       => [
        'string' => ':attribute は :max 文字以下で入力してください。',
        'numeric'=> ':attribute は :max 以下で入力してください。',
        'array'  => ':attribute は :max 個以下で選択してください。',
    ],
    'confirmed' => ':attribute と確認用が一致しません',
    'unique'    => ':attribute はすでに使用されています。',
    'regex'     => ':attribute の形式が正しくありません。',
    'image'     => ':attribute は画像ファイルをアップロードしてください。',
    'mimes'     => ':attribute は :values 形式の画像をアップロードしてください。',
    'mimetypes' => ':attribute は :values 形式のファイルをアップロードしてください。',
    'exists'    => '選択された :attribute は存在しません。',
    'in'        => '選択された :attribute が不正です。',

    // 属性名
    'attributes' => [
        // 認証系
        'name'                  => 'お名前',
        'email'                 => 'メールアドレス',
        'password'              => 'パスワード',
        'password_confirmation' => '確認用パスワード',

        // プロフィール／住所
        'postal_code' => '郵便番号',
        'address'     => '住所',
        'building'    => '建物名',

        // 出品（商品）
        'image'       => '商品画像',
        'title'       => '商品名',
        'brand'       => 'ブランド名',
        'description' => '商品説明',
        'category_id' => 'カテゴリー',
        'condition'   => '商品の状態',
        'price'       => '価格',
        'profile_image' => 'プロフィール画像',
    ],
    'custom' => [
        'name'  => [
            'required' => 'お名前を入力してください',
        ],
        'email' => [
            'required' => 'メールアドレスを入力してください',
            'email'    => 'メールアドレスはメール形式で入力してください',
            'unique'   => 'このメールアドレスは既に登録されています',
        ],
        'password' => [
            'required'  => 'パスワードを入力してください',
            'min'       => 'パスワードは8文字以上で入力してください',
            'confirmed' => 'パスワードと一致しません',
        ],
        'password_confirmation' => [
            'required' => '確認用パスワードを入力してください',
        ],
        'auth' => [
            'failed' => 'ログイン情報が登録されていません',
        ],
    ],
];