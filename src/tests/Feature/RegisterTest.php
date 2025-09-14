<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    public function 名前が未入力だとバリデーションエラーになる(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_email_is_required(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => '',  // 空にする
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_password_is_required(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_password_must_be_at_least_8_characters(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_password_and_confirmation_must_match(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_user_can_register_successfully(): void
{
    Notification::fake(); // ← 実メール送信を止める

    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->post('/register', $payload);

    // 登録後は認証メール案内ページへ
    $response->assertRedirect(route('verification.notice'));

    // ユーザーが作成されたこと
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name'  => 'Test User',
    ]);

    $user = User::where('email', 'test@example.com')->first();
    $this->assertNotNull($user, 'User was not created');

    // パスワードがハッシュ保存であること（平文ではない）
    $this->assertNotEquals('password123', $user->password);

    // 検証メールがそのユーザー宛に通知されたこと
    Notification::assertSentTo($user, VerifyEmail::class);
}
    public function test_email_verification_redirects_to_profile_edit_on_first_login(): void
{
    // 通知をフェイク（実メールは送らない）
    Notification::fake();

    // 新規登録（正しい入力）
    $payload = [
        'name' => 'First Login User',      // ユーザー名
        'email' => 'first@example.com',    // メール
        'password' => 'password123',       // パスワード
        'password_confirmation' => 'password123', // 確認
    ];
    $this->post('/register', $payload)
         // 登録後は認証案内へ
            ->assertRedirect(route('verification.notice'));

    // ユーザーが作成され、認証メールがキューされたことを確認
    $user = User::where('email', 'first@example.com')->firstOrFail();
    Notification::assertSentTo($user, VerifyEmail::class);

    // 署名付きの認証URLを自前で作って踏む
    // 認証ルートは 'auth' + 'signed' ミドルウェアが必要 → actingAs でログイン状態に
    $this->actingAs($user);

    // 認証URL（有効期限付きの署名URL）を生成
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',            // ルート名
        now()->addMinutes(60),            // 有効期限
        ['id' => $user->id, 'hash' => sha1($user->email)] // パラメータ
    );

    // 認証URLにアクセス → 初回プロフィール編集へ
    $this->get($verificationUrl)
            ->assertRedirect(route('profile.edit'));

    // DB上で認証済みになっていること
    $this->assertNotNull($user->fresh()->email_verified_at, 'メール認証が完了していません');
}
}
