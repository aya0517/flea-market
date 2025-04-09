<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    public function test_address_changes_are_reflected_on_purchase_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post("/purchase/address/{$item->id}", [
            'postal_code' => '100-0001',
            'address' => '東京都千代田区',
            'building_name' => 'テストビル 201',
        ]);

        $response = $this->get("/purchase/{$item->id}");

        $response->assertSee('100-0001');
        $response->assertSee('東京都千代田区');
        $response->assertSee('テストビル 201');
    }

    public function test_address_is_saved_in_user_profile()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post("/purchase/address/{$item->id}", [
            'postal_code' => '173-0001',
            'address' => '東京都板橋区',
            'building_name' => 'テストビル 303',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'postal_code' => '173-0001',
            'address' => '東京都板橋区',
            'building_name' => 'テストビル 303',
        ]);
    }
}
