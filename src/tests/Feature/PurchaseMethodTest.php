<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    public function test_guest_cannot_access_payment_process()
    {
        $item = Item::factory()->create();

        $response = $this->post('/purchase/process', [
            'item_id' => $item->id,
            'payment_method' => 'card'
        ]);

        $response->assertRedirect('/login');
    }

    public function test_user_can_start_card_payment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/purchase/process', [
            'item_id' => $item->id,
            'payment_method' => 'card'
        ]);

        $response->assertRedirect();
    }

    public function test_user_can_start_konbini_payment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/purchase/process', [
            'item_id' => $item->id,
            'payment_method' => 'konbini'
        ]);

        $response->assertRedirect();
    }

    public function test_sold_item_payment_is_rejected()
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