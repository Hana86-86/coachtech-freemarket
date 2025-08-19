<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // 出品者
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // 商品情報
            $table->string('title', 255);
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            // 分類
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            // 価格・状態
            $table->unsignedInteger('price');
            $table->enum('condition', [
                '新品・未使用',
                '未使用に近い',
                '良好',
                '目立った傷や汚れなし',
                'やや傷や汚れあり',
                '状態が悪い'
            ])->default('良好');
            // 画像・ステータス
            $table->string('image_path', 255);
            // 販売ステータス
            $table->enum('sale_status', ['公開中', '取引中', '売却済'])->default('公開中');
            // インデックス（検索高速化）
            $table->index('category_id');
            $table->index(['sale_status', 'category_id']); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
