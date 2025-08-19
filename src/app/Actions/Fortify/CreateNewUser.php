<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Auth\Events\Registered;

class CreateNewUser implements CreatesNewUsers
{

public function create(array $input)
{
    Validator::make($input,
    [
        // ユーザー名(必須・最大20文字)
        'name'         => ['required', 'string', 'max:20'],
        // メール(必須・形式・重複不可)
        'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
        // パスワード(必須・8文字以上・確認一致)
        'password'     => ['required', 'string', 'min:8', 'confirmed'],
    ],
    // 日本語メッセージ
    [
        'required'    => ':attribute を入力してください。',
        'string'      => ':attribute は文字列で入力してください。',
        'max'         => ':attribute は :max 文字以内で入力してください。',
        'email'       => ':attribute はメール形式で入力してください。',
        'unique'      => 'この :attribute はすでに使用されています。',
        'min'         => ':attribute は :min 文字以上で入力してください。',
        'confirmed'   => ':attribute と確認用が一致しません。',
    ],
    // 属性名
    [
        'name'    => 'お名前',
        'email'   => 'メールアドレス',
        'password'=> 'パスワード',
    ]
    )->validate();

    $user =  User::create([
    'name' => $input['name'],
    'email' => $input['email'],
    'password' => Hash::make($input['password']),
]);

// 登録イベントを発火->メール送信
event(new Registered($user));

return $user;
}
}