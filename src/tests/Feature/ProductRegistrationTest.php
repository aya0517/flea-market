<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\User;
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

        Condition::factory()->create(['id' => 1, 'name' => '良好']);
        Category::factory()->create();
    }

    public function test_authenticated_user_can_register_product()
    {
        $user = User::factory()->create();
        $category = Category::first();

        $this->actingAs($user);
        $file = new UploadedFile(
            base_path('tests/Fixtures/dummy.jpg'),
            'dummy.jpg',
            'image/jpeg',
            null,
            true
        );

        $response = $this->post('/sell', [
            'name' => 'テスト商品',
            'price' => 3000,
            'description' => 'テスト用の商品説明です。',
            'condition_id' => 1,
            'categories' => [$category->id],
            'image' => $file,
        ]);

        $response->assertRedirect('/mypage');

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'price' => 3000,
            'description' => 'テスト用の商品説明です。',
            'condition_id' => 1,
            'user_id' => $user->id,
        ]);

        $item = \App\Models\Item::latest()->first();
        $this->assertFileExists(public_path($item->image_path));
    }

    public function test_product_registration_requires_validation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/sell', []);

        $response->assertSessionHasErrors([
            'name', 'price', 'description', 'condition_id', 'categories', 'image'
        ]);
    }
}
