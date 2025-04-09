<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    public function test_user_profile_information_is_displayed()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー'
        ]);

        $sellingItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品商品'
        ]);
        $boughtItem = Item::factory()->create([
            'buyer_id' => $user->id,
            'name' => '購入商品'
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage.index', ['page' => 'sell']));
        $response->assertSee('出品商品');

        $response = $this->get(route('mypage.index', ['page' => 'buy']));
        $response->assertSee('購入商品');
    }

    public function test_user_edit_form_has_initial_values()
    {
        $user = User::factory()->create();

        UserProfile::factory()->create([
            'user_id' => $user->id,
            'username' => '編集前ユーザー',
            'postal_code' => '101-0001',
            'address' => '東京都千代田区',
            'building_name' => '初期ビル301',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage.profile.edit'));

        $response->assertSee('value="編集前ユーザー"', false);
        $response->assertSee('value="101-0001"', false);
        $response->assertSee('value="東京都千代田区"', false);
        $response->assertSee('value="初期ビル301"', false);
    }
}