<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    public function test_user_can_like_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->post(route('favorites.store'), ['item_id' => $item->id]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_user_can_unlike_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Favorite::create(['user_id' => $user->id, 'item_id' => $item->id]);

        $this->actingAs($user)
            ->delete(route('favorites.destroy', ['item' => $item->id]));

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_like_count_increases_when_liked()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->post(route('favorites.store'), ['item_id' => $item->id]);

        $this->assertEquals(1, Favorite::where('item_id', $item->id)->count());
    }

    public function test_like_count_decreases_when_unliked()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Favorite::create(['user_id' => $user->id, 'item_id' => $item->id]);

        $this->actingAs($user)
            ->delete(route('favorites.destroy', ['item' => $item->id]));

        $this->assertEquals(0, Favorite::where('item_id', $item->id)->count());
    }
}
