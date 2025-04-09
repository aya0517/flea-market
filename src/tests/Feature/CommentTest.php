<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\ConditionSeeder::class);
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

    public function test_validation_error_when_comment_is_empty()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $response = $this->post(route('comments.store', ['item' => $item->id]), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_validation_error_when_comment_exceeds_255_characters()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $response = $this->post(route('comments.store', ['item' => $item->id]), [
            'content' => str_repeat('あ', 256),
        ]);

        $response->assertSessionHasErrors('content');
    }
}
