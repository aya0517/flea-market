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

    public function test_selected_payment_method_is_sent_correctly()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['buyer_id' => null]);

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
}