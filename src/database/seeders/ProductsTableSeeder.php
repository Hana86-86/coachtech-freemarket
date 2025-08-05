<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
        [  'user_id' => 1,
            'title' => '腕時計',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'price' => 15000,
            'category' => 'Rolax',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'condition' => '良好',
            'sale_status' => '公開中',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_id' => 1,
            'title' => 'HDD',
            'description' => '高速で信頼性の高いハードディスク',
            'price' => 5000,
            'category' => '西芝',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
            'condition' => '目立った傷や汚れなし',
            'sale_status' => '公開中',
            'created_at' => now(),
            'updated_at' =>now(),
        ],
        [
            'user_id' => 1,
            'title' => '玉ねぎ３束',
            'description' => '新鮮な玉ねぎ３束セット',
            'price' => 300,
            'category' => 'なし',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
            'condition' => 'やや傷や汚れあり',
            'sale_status' => '公開中',
            'created_at' => now(),
            'updated_at' =>now(),

        ],
        [
            'user_id' => 1,
            'title' => '革靴',
            'description' => 'クラシックなデザインの革靴',
            'price' => 4000,
            'category' => null,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
            'condition' => '状態が悪い',
            'sale_status' => '公開中',
            'created_at' => now(),
            'updated_at' =>now(),

        ],
        [   'user_id' => 1,
            'title' => 'ノートPC',
            'description' => '高性能なノートパソコン',
            'price' => 45000,
            'category' => null,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
            'condition' => '良好',
            'sale_status' => '公開中',
            'created_at' => now(),
            'updated_at' =>now(),

        ],
        [   'user_id' => 1,
            'title' => 'マイク',
            'description' => '高音質なレコーディング用マイク',
            'price' => 8000,
            'category' => 'なし',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
            'condition' => '目立った傷や汚れなし',
            'sale_status' => '公開中',
            'created_at' => now(),
            'updated_at' =>now(),


        ],
        [   'user_id' => 1,
            'title' => 'ショルダーバッグ',
            'description' => 'おしゃれなショルダーバッグ',
            'price' => 3500,
            'category' => null,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
            'condition' => 'やや傷や汚れあり',
            'sale_status' => '公開中',
            'created_at' => now(),
            'updated_at' =>now(),

        ],
        [   'user_id' => 1,
            'title' => 'タンブラー',
            'description' => '使いやすいタンブラー',
            'price' => 500,
            'category' => 'なし',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
            'condition' => '状態が悪い',
            'sale_status' => '公開中',
            'created_at' => now(),
            'updated_at' =>now(),

        ],
        [   'user_id' => 1,
            'title' => 'コーヒーミル',
            'description' => '手動のコーヒーミル',
            'price' => 4000,
            'category' => 'Starbacks',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'condition' => '良好',
            'sale_status' => '公開中',
            'created_at' => now(),
            'updated_at' =>now(),

        ],
        [   'user_id' => 1,
            'title' => 'メイクセット',
            'description' => '便利なメイクアップセット',
            'price' => 2500,
            'category' => null,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'condition' => '目立った傷や汚れなし',
            'sale_status' => '公開中',
            'created_at' => now(),
            'updated_at' =>now(),

        ],
    ]);
}
}
