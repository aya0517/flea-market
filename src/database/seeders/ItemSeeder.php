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
        $loginUser = User::factory()->create([
            'name' => 'ログインユーザー',
            'email' => '1@example.com',
            'password' => bcrypt('password123'),
        ]);

        User::factory(9)->create();

        $users = User::where('id', '!=', $loginUser->id)->pluck('id');
        $categories = Category::pluck('id');
        $categoryMap = Category::pluck('id', 'name');

        $specificUser = User::where('email', 'test@example.com')->first();

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'images/items/Armani+Mens+Clock.jpg',
                'condition_id' => 1,
                'user_id' => $users->random(),
                'category_names' => ['ファッション', 'メンズ','アクセサリー'],
                'brand' => 'アルマーニ',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'images/items/HDD+Hard+Disk.jpg',
                'condition_id' => 2,
                'user_id' => $specificUser->id,
                'category_names' => ['家電', 'インテリア'],
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'images/items/iLoveIMG+d.jpg',
                'condition_id' => 3,
                'user_id' => $users->random(),
                'category_names' => ['キッチン',]
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'images/items/Leather+Shoes+Product+Photo.jpg',
                'condition_id' => 4,
                'user_id' => $specificUser->id,
                'category_names' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image_path' => 'images/items/Living+Room+Laptop.jpg',
                'condition_id' => 1,
                'user_id' => $users->random(),
                'category_names' => ['家電'],
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'images/items/Music+Mic+4632231.jpg',
                'condition_id' => 2,
                'user_id' => $users->random(),
                'category_names' => ['家電'],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'images/items/Purse+fashion+pocket.jpg',
                'condition_id' => 3,
                'user_id' =>$users->random(),
                'category_names' => ['ファッション', 'レディース'],
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image_path' => 'images/items/Tumbler+souvenir.jpg',
                'condition_id' => 4,
                'user_id' => $users->random(),
                'category_names' => ['家電'],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image_path' => 'images/items/Waitress+with+Coffee+Grinder.jpg',
                'condition_id' => 1,
                'user_id' => $users->random(),
                'category_names' => ['家電', 'キッチン'],
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image_path' => 'images/items/外出メイクアップセット.jpg',
                'condition_id' => 2,
                'user_id' => $users->random(),
                'category_names' => ['レディース', 'コスメ','ファッション'],
            ],
        ];

        foreach ($items as $itemData) {
            $categoryIds = collect($itemData['category_names'])->map(fn($name) => $categoryMap[$name])->toArray();

            unset($itemData['category_names']);

            $itemData['user_id'] = $itemData['user_id'] ?? $users->random();

            $item = Item::create($itemData);
            $item->categories()->attach($categoryIds);

            if ($item->name === 'ノートPC') {
                $item->buyer_id = $specificUser->id;
                $item->save();
            }
        }
    }
}
