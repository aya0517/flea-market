<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\ConditionSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    public function test_authenticated_user_can_register_product()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $conditionId = Condition::first()->id;

        $this->actingAs($user);

        $image = UploadedFile::fake()->image('test.jpg');

        $response = $this->post(route('sell.store'), [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 3000,
            'description' => '商品の説明文です。',
            'condition_id' => $conditionId,
            'categories' => [$category->id],
            'image' => $image,
        ]);

        $response->assertRedirect(route('mypage.index'));

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 3000,
            'description' => '商品の説明文です。',
            'condition_id' => $conditionId,
            'user_id' => $user->id,
        ]);

        $imagePath = 'images/items/' . time() . '_test.jpg';
        Storage::disk('public')->assertExists($imagePath);
    }
}