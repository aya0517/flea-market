<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\ConditionSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    public function test_authenticated_user_can_view_product_details_with_all_info()
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $condition = Condition::first();

        $item = Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'price' => 5000,
            'description' => '商品の説明です。',
            'image_path' => 'images/items/test_image.jpg',
        ]);

        $item->categories()->attach([$category1->id, $category2->id]);

        $comment = Comment::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => '素晴らしい商品です！',
        ]);

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('items.detail', ['item' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee('￥5,000');
        $response->assertSee($item->description);
        $response->assertSee($item->image_path);
        $response->assertSee($item->condition->name);
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
        $response->assertSee($comment->content);
        $response->assertSee($comment->user->name);
        $response->assertSee('コメント (1)');
        $response->assertSee('1');
    }

    public function test_authenticated_user_can_view_product_details_with_multiple_categories()
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $condition = Condition::first();

        $item = Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'price' => 5000,
            'description' => '商品の説明です。',
            'image_path' => 'images/items/test_image.jpg',
        ]);

        $item->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get(route('items.detail', ['item' => $item->id]));

        $response->assertStatus(200);

        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
    }

}
