<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_unverified_user_is_redirected_to_verification_notice_on_login(): void
    {
        // 認証未完了ユーザーを作成（email_verified_at = null）
        $user = User::factory()->create([
            'email' => 'u1@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => null,     // 未認証
            'is_first_login' => 1,           // 初回ログインフラグON（任意）
            'profile_completed' => 0,        // プロフィール未完了（任意）
        ]);

        // ログイン（POST /login）
        $this->post('/login', [
            'email' => 'u1@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('verification.notice')); // 認証案内へ
    }

    /**
     * 認証済み + 初回ログイン → プロフィール編集へ
     */
    public function test_first_login_redirects_to_profile_edit(): void
    {
        //  認証済み & 初回ログインのユーザー
        $user = User::factory()->create([
            'email' => 'u2@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),    // 認証済み！
            'is_first_login' => 1,           // 初回ログインフラグON
            'profile_completed' => 0,        // プロフィール未完了
        ]);

        $this->post('/login', [
            'email' => 'u2@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('profile.edit'));       // 初回はプロフィール編集へ
    }

    /**
     * 認証済み + 2回目以降（プロフィール完了）→ 商品一覧へ
     */
    public function test_after_profile_completed_login_redirects_to_products_index(): void
    {
        // ◆ 認証済み & 初回でない & プロフィール完了
        $user = User::factory()->create([
            'email' => 'u3@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),    // 認証済み！
            'is_first_login' => 0,           // 2回目以降
            'profile_completed' => 1,        // プロフィール完了済み
        ]);

        $this->post('/login', [
            'email' => 'u3@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('products.index'));     // 2回目以降は一覧へ
    }
}
