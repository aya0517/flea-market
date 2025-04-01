<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    public function test_guest_can_view_product_detail()
    {
        $item = Item::factory()->create();
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
    }

    public function test_product_detail_displays_all_essential_information()
    {
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'price' => 9999,
            'description' => 'これは説明文です',
            'condition_id' => 1,
        ]);

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('9,999');
        $response->assertSee('これは説明文です');
    }

    public function test_user_can_post_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $response = $this->post("/items/{$item->id}/comments", [
            'content' => '購入できますか？'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => '購入できますか？'
        ]);
    }

    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post("/items/{$item->id}/comments", [
            'content' => 'ログインしてないコメント'
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', [
            'content' => 'ログインしてないコメント'
        ]);
    }

    public function test_user_can_see_comment_count_and_details()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        Comment::factory()->count(2)->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);

        $response = $this->get('/item/' . $item->id);
        $response->assertSee('テストコメント');
    }

    public function test_purchase_button_navigates_to_confirmation()
    {
        $item = Item::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/purchase/' . $item->id);

        $response->assertStatus(200);
        $response->assertViewIs('purchase.show');
    }
}