<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    public function test_guest_cannot_access_purchase_page()
    {
        $item = Item::factory()->create();
        $response = $this->get('/purchase/' . $item->id);
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_purchase_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        UserProfile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get('/purchase/' . $item->id);
        $response->assertStatus(200);
        $response->assertViewIs('purchase.show');
        $response->assertSee($item->name);
    }

    public function test_user_can_update_shipping_address()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $response = $this->post('/purchase/address/' . $item->id, [
            'postal_code' => '123-4567',
            'address' => '東京都港区',
            'building_name' => 'アパート101'
        ]);

        $response->assertRedirect(route('mypage.index'));
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都港区',
            'building_name' => 'アパート101'
        ]);
    }

    public function test_sold_item_cannot_be_purchased_again()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => true]);

        $this->actingAs($user);
        $response = $this->post('/purchase/process', [
            'item_id' => $item->id,
            'payment_method' => 'card'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'この商品は既に購入されています。');
    }
}
