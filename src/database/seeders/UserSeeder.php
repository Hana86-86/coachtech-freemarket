<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Profile;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => '1234567',
                'address' => '東京都渋谷区1-1-1',
                'building' => 'テストビル101',
                'profile_image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
