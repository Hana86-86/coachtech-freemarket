<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'user_id'       => 1,
                'title'         => '腕時計',
                'brand'         => 'Rolex',
                'description'   => 'スタイリッシュなデザインのメンズ腕時計',
                'price'         => 15000,
                'condition'     => '良好',
                'category_name' => 'アクセサリー',
                'image_file'    => 'mens-clock.jpg',
            ],
            [
                'user_id'       => 1,
                'title'         => 'HDD',
                'brand'         => null,
                'description'   => '高速で信頼性の高いハードディスク',
                'price'         => 5000,
                'condition'     => '目立った傷や汚れなし',
                'category_name' => '家電',
                'image_file'    => 'hard-disc.jpg',
            ],
            [
                'user_id'       => 1,
                'title'         => '玉ねぎ3束',
                'brand'         => null,
                'description'   => '新鮮な玉ねぎ3束のセット',
                'price'         => 300,
                'condition'     => 'やや傷や汚れあり',
                'category_name' => 'キッチン',
                'image_file'    => 'onion.jpg',
            ],
            [
                'user_id'       => 1,
                'title'         => '革靴',
                'brand'         => 'REGAL',
                'description'   => 'クラシックなデザインの革靴',
                'price'         => 4000,
                'condition'     => '状態が悪い',
                'category_name' => 'ファッション',
                'image_file'    => 'leather-shoes.jpg',
            ],
            [
                'user_id'       => 1,
                'title'         => 'ノートPC',
                'brand'         => 'Apple',
                'description'   => '高性能なノートパソコン',
                'price'         => 45000,
                'condition'     => '良好',
                'category_name' => '家電',
                'image_file'    => 'living-laptop.jpg',
            ],
            [
                'user_id'       => 2,
                'title'         => 'マイク',
                'brand'         => null,
                'description'   => '高音質のレコーディング用マイク',
                'price'         => 8000,
                'condition'     => '目立った傷や汚れなし',
                'category_name' => '家電',
                'image_file'    => 'music-mic.jpg',
            ],
            [
                'user_id'       => 2,
                'title'         => 'ショルダーバッグ',
                'brand'         => null,
                'description'   => 'おしゃれなショルダーバッグ',
                'price'         => 3500,
                'condition'     => 'やや傷や汚れあり',
                'category_name' => 'ファッション',
                'image_file'    => 'fashion-bag.jpg',
            ],
            [
                'user_id'       => 2,
                'title'         => 'タンブラー',
                'brand'         => 'Starbucks',
                'description'   => '使いやすいタンブラー',
                'price'         => 1500,
                'condition'     => '状態が悪い',
                'category_name' => 'キッチン',
                'image_file'    => 'tumbler.jpg',
            ],
            [
                'user_id'       => 2,
                'title'         => 'コーヒーミル',
                'brand'         => null,
                'description'   => '手動のコーヒーミル',
                'price'         => 4000,
                'condition'     => '良好',
                'category_name' => 'キッチン',
                'image_file'    => 'coffee-grinder.jpg',
            ],
            [
                'user_id'       => 2,
                'title'         => 'メイクセット',
                'brand'         => null,
                'description'   => '便利なメイクアップセット',
                'price'         => 2500,
                'condition'     => '目立った傷や汚れなし',
                'category_name' => 'コスメ',
                'image_file'    => 'outing-makeup-set.jpg',
            ],
        ];

        foreach ($rows as $row) {

            $categoryId = DB::table('categories')
                ->where('name', $row['category_name'])
                ->value('id');

            $product_id = DB::table('products')->insertGetId([
                'user_id'    => $row['user_id'],
                'title'      => $row['title'],
                'brand'      => $row['brand'],
                'description'=> $row['description'],
                'price'      => $row['price'],
                'condition'  => $row['condition'],
                'image_path'  => 'storage/products/' . $row['image_file'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('category_product')->insert([
                'product_id'  => $product_id,
                'category_id' => $categoryId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}