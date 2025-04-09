<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Condition::insert([
            ['id' => 1, 'name' => '良好'],
            ['id' => 2, 'name' => '目立った傷や汚れなし'],
            ['id' => 3, 'name' => 'やや傷や汚れあり'],
            ['id' => 4, 'name' => '状態が悪い'],
        ]);
    }

    public function test_all_items_are_displayed()
    {
        Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(Item::first()->name);
    }

    public function test_sold_items_show_sold_label()
    {
        $user = \App\Models\User::factory()->create();
        $otherUser = User::factory()->create();

        $soldItem = Item::factory()->create([
            'name' => 'テストSOLD商品',
            'user_id' => $otherUser->id,
            'buyer_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertSee('Sold');
    }


    public function test_user_cannot_see_their_own_items()
    {
        $user = User::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id, 'name' => 'test',]);

        $this->actingAs($user);
        $response = $this->get('/');

        $response->assertDontSee($ownItem->name);
    }
}
