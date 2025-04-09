<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    public function test_user_can_purchase_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => false]);

        $this->actingAs($user);

        $response = $this->post('/purchase/process', [
            'item_id' => $item->id,
            'payment_method' => 'konbini',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'buyer_id' => $user->id,
        ]);
    }

    public function test_purchased_item_shows_sold_label()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'buyer_id' => $user->id,
            'name' => '購入済み商品',
        ]);

        $response = $this->actingAs($user)->get('/?tab=recommended');
        $response->assertSee('Sold');
    }

    public function test_purchased_item_appears_in_profile_purchase_list()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'buyer_id' => $user->id,
            'name' => 'マイ購入品',
        ]);

        $this->actingAs($user);
        $response = $this->get(route('mypage.index'));

        $response->assertSee('マイ購入品');
    }
}
