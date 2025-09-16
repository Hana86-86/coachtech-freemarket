<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function search_bar_exists_in_header(): void
    {
        $this->get(route('products.index'))
            ->assertStatus(200)
            ->assertSee('name="keyword"', false);
    }

    /** @test */
    public function can_search_by_partial_match_on_title(): void
    {
        // 「黒 革靴」はヒット、「革 バッグ」は非ヒット
        Product::factory()->create(['title' => '黒 革靴']);
        Product::factory()->create(['title' => '革 バッグ']);

        $this->get(route('products.index', ['keyword' => '靴']))
            ->assertStatus(200)
            ->assertSee('黒 革靴')
            ->assertDontSee('革 バッグ');
    }

    /** @test */
    public function keyword_is_kept_on_mylist_tab(): void
    {
        $user = User::factory()->create();

        // 商品を作成 & お気に入り（=マイリスト）へ
        $product = Product::factory()->create([
            'title'       => '黒 革靴',
            'sale_status' => Product::SALE_STATUS_PUBLIC,
        ]);
        $user->favorites()->create(['product_id' => $product->id]);

        // ログインしてマイリストタブへ遷移時に keyword が URL に残ること
        $this->actingAs($user)
            ->get(route('products.index', ['tab' => 'mylist', 'keyword' => '靴']))
            ->assertStatus(200)
            ->assertSee('黒 革靴');
    }
    /** @test */
    public function search_works_on_osusume_tab(): void
{
    // ヒットしてほしい / してほしくないデータ
        Product::factory()->create(['title' => '黒 革靴']);
        Product::factory()->create(['title' => '赤 バッグ']);

    // おすすめタブ（tab=all）で「靴」を検索
        $this->get(route('products.index', ['tab' => 'all', 'keyword' => '靴']))
        ->assertStatus(200)
        ->assertSee('黒 革靴')
        ->assertDontSee('赤 バッグ');

    // レスポンス内の検索フォームに hidden の tab=all が残っていることも確認
        $this->get(route('products.index', ['tab' => 'all', 'keyword' => '靴']))
        ->assertSee('name="tab"', false)
        ->assertSee('value="all"', false);
}
}