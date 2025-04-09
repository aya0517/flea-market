<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Favorite;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\ConditionSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    public function test_authenticated_user_can_search_product_by_name()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();

        $item1 = Item::factory()->create([
            'name' => 'テスト商品',
            'condition_id' => $condition->id,
        ]);

        $item1->categories()->attach($category->id);

        $item2 = Item::factory()->create([
            'name' => 'ショルダーバッグ',
            'condition_id' => $condition->id,
        ]);
        $item2->categories()->attach($category->id);

        $this->actingAs($user);

        $response = $this->get('/?search=テスト商品');

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertDontSee('ショルダーバッグ');
    }

    public function test_search_keyword_is_preserved_in_mylist()
    {
        $user = User::factory()->create();
        $item1 = Item::factory()->create(['name' => 'ショルダーバッグ']);
        $item2 = Item::factory()->create(['name' => 'ノート']);

        Favorite::create(['user_id' => $user->id, 'item_id' => $item1->id]);
        Favorite::create(['user_id' => $user->id, 'item_id' => $item2->id]);

        $this->actingAs($user);
        $response = $this->get('/?tab=mylist&search=ショルダー');

        $response->assertSee('ショルダーバッグ');
        $response->assertDontSee('ノート');
    }
}
