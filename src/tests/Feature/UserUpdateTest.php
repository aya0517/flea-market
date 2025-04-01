<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    public function test_guest_cannot_access_shipping_address_edit()
    {
        $item = Item::factory()->create();
        $response = $this->get('/purchase/address/' . $item->id);
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_address_edit_form()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/purchase/address/' . $item->id);

        $response->assertStatus(200);
        $response->assertViewIs('purchase.address_edit');
    }

    public function test_user_can_update_shipping_address()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/purchase/address/' . $item->id, [
            'postal_code' => '123-4567',
            'address' => '東京都千代田区',
            'building_name' => 'テストビル 201'
        ]);

        $response->assertRedirect(route('mypage.index'));

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都千代田区',
            'building_name' => 'テストビル 201',
        ]);
    }

    public function test_address_update_requires_validation()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/purchase/address/' . $item->id, [
            'postal_code' => '',
            'address' => '',
        ]);

        $response->assertSessionHasErrors(['postal_code', 'address']);
    }
}