<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            [
                'user_id' => 1,
                'title' => '腕時計',
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'category_name' => 'ファッション',
                'price' => 15000,
                'condition' => '良好',
                'image_file' => 'mens-clock.jpg',
            ],
            [
                'user_id' => 1,
                'title' => 'HDD',
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'category_name' => '家電',
                'price' => 5000,
                'condition' => '目立った傷や汚れなし',
                'image_file' => 'hard-disc.jpg',
            ],
            [
                'user_id' => 1,
                'title' => '玉ねぎ３束',
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ３束セット',
                'category_name' => 'キッチン',
                'price' => 300,
                'condition' => 'やや傷や汚れあり',
                'image_file' => 'onion.jpg',
            ],
            [
                'user_id' => 1,
                'title' => '革靴',
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'category_name' => 'メンズ',
                'price' => 4000,
                'condition' => '状態が悪い',
                'image_file' => 'leather-shoes.jpg',
            ],
            [
                'user_id' => 1,
                'title' => 'ノートPC',
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'category_name' => 'ゲーム',
                'price' => 45000,
                'condition' => '良好',
                'image_file' => 'living-laptop.jpg',
            ],
            [
                'user_id' => 1,
                'title' => 'マイク',
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'category_name' => '家電',
                'price' => 8000,
                'condition' => '目立った傷や汚れなし',
                'image_file' => 'music-mic.jpg',
            ],
            [
                'user_id' => 1,
                'title' => 'ショルダーバッグ',
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'category_name' => 'ファッション',
                'price' => 3500,
                'condition' => 'やや傷や汚れあり',
                'image_file' => 'fashion-bag.jpg',
            ],
            [
                'user_id' => 1,
                'title' => 'タンブラー',
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'category_name' => 'キッチン',
                'price' => 500,
                'condition' => '状態が悪い',
                'image_file' => 'tumbler.jpg',
            ],
            [
                'user_id' => 1,
                'title' => 'コーヒーミル',
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'category_name' => 'キッチン',
                'price' => 4000,
                'condition' => '良好',
                'image_file' => 'coffee-grinder.jpg',
            ],
            [
                'user_id' => 1,
                'title' => 'メイクセット',
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'category_name' => 'コスメ',
                'price' => 2500,
                'condition' => '目立った傷や汚れなし',
                'image_file' => 'outing-makeup-set.jpg',
            ],
        ];

        foreach($rows as $row) {
            $categoryId = null;
            if(!empty($row['category_name'])) {
                $categoryId = DB::table('categories')
                    ->where('name', $row['category_name'])
                    ->value('id');
            }

            DB::table('products')->insert([
                'user_id' => $row['user_id'],
                'title' => $row['title'],
                'brand' => $row['brand'] ?? null,
                'description' => $row['description'] ?? null,
                'category_id' => $categoryId,
                'price' => $row['price'],
                'condition' => $row['condition'],
                'image_path' => 'storage/products/' . $row['image_file'],
                'sale_status' => '公開中',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


    }
}
