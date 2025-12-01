<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ★ 3人分のユーザーデータを配列で用意
        $users = [
            [
                'email' => 'seller1@example.com',   // C001〜C005を出品する人
                'name'  => '出品者A',
            ],
            [
                'email' => 'seller2@example.com',   // C006〜C010を出品する人
                'name'  => '出品者B',
            ],
            [
                'email' => 'buyer@example.com',     // 何も出品していない購入者
                'name'  => '購入者ユーザー',
            ],
        ];

        foreach ($users as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'       => $data['name'],
                    'password'   => Hash::make('password'), // 全員パスワードは「password」
                    'email_verified_at'=> now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'postal_code' => '1234567',
                    'address'     => '東京都テスト市1-1-1',
                    'building'    => 'テストビル'.$index.'01',
                    'profile_image' => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }
    }
}