<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first(); // 最初のユーザーを取得

        DB::table('profiles')->insert([
            'user_id' => $user->id,
            'phone' => '09012345678',
            'postal_code' => '1234567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル101',
            'profile_image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
