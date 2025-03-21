<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    public function run()
    {
        // ユーザーを作成
        User::factory(10)->create();
        $users = DB::table('users')->pluck('id');

        // 既存のカテゴリーを取得
        $categories = Category::pluck('id');

        // 商品データを作成
        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_url' => 'images/items/Armani+Mens+Clock.jpg',
                'condition_id' => 1, // 良好
                'user_id' => $users->random(),
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image_url' => 'images/items/HDD+Hard+Disk.jpg',
                'condition_id' => 2, // 目立った傷や汚れなし
                'user_id' => $users->random(),
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_url' => 'images/items/iLoveIMG+d.jpg',
                'condition_id' => 3, // やや傷や汚れあり
                'user_id' => $users->random(),
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image_url' => 'images/items/Leather+Shoes+Product+Photo.jpg',
                'condition_id' => 4, // 状態が悪い
                'user_id' => $users->random(),
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image_url' => 'images/items/Living+Room+Laptop.jpg',
                'condition_id' => 1, // 良好
                'user_id' => $users->random(),
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image_url' => 'images/items/Music+Mic+4632231.jpg',
                'condition_id' => 2, // 目立った傷や汚れなし
                'user_id' => $users->random(),
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image_url' => 'images/items/Purse+fashion+pocket.jpg',
                'condition_id' => 3, // やや傷や汚れあり
                'user_id' =>$users->random(),
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image_url' => 'images/items/Tumbler+souvenir.jpg',
                'condition_id' => 4, // 状態が悪い
                'user_id' => $users->random(),
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image_url' => 'images/items/Waitress+with+Coffee+Grinder.jpg',
                'condition_id' => 1, // 良好
                'user_id' => $users->random(),
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image_url' => 'images/items/外出メイクアップセット.jpg',
                'condition_id' => 2, // 目立った傷や汚れなし
                'user_id' => $users->random(),
            ],
        ];

        foreach ($items as $itemData) {
            // 商品を作成
            $item = Item::create($itemData);

            // ランダムで1〜3個のカテゴリを設定
            $randomCategories = $categories->random(rand(1, 3));
            $item->categories()->attach($randomCategories);
        }
    }
}
