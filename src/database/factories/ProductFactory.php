<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\User;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'title'       => $this->faker->words(2, true),// 2語のランダムな文字列
            'description' => $this->faker->sentence(),
            'condition'   => 1,                              // ★ 数値に固定（DB定義に合わせる）
            'price'       => $this->faker->numberBetween(1000, 50000),
            'image_path'  => 'dummy.jpg',
            'sale_status' => Product::SALE_STATUS_PUBLIC,
        ];
    }
}