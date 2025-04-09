<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Favorite;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    public function test_only_liked_items_are_displayed()
    {
        $user = User::factory()->create();
        $likedItem = Item::factory()->create();
        $notLikedItem = Item::factory()->create();

        Favorite::create(['user_id' => $user->id, 'item_id' => $likedItem->id]);

        $this->actingAs($user);
        $response = $this->get('/?tab=mylist');

        $response->assertSee($likedItem->name);
        $response->assertDontSeeText($notLikedItem->name);
    }

    public function test_purchased_items_show_sold_label()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'buyer_id' => $user->id,
            'name' => 'テスト商品',
        ]);

        Favorite::create(['user_id' => $user->id, 'item_id' => $item->id]);

        $this->actingAs($user);
        $response = $this->get('/?tab=mylist');

        $response->assertSee('Sold');
    }


    public function test_guest_sees_nothing_on_mylist()
    {
        $response = $this->get('/?tab=mylist');
        $response->assertSee('該当する商品がありません');
        $response->assertStatus(200);
    }

}
