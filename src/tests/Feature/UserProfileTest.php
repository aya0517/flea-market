<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile_edit_page()
    {
        $response = $this->get('/mypage/profile');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_profile_edit_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);
        $response->assertViewIs('mypage.profile');
    }

    public function test_user_can_update_profile_first_login()
    {
        $user = User::factory()->create(['first_login' => true]);
        $this->actingAs($user);

        $response = $this->post('/mypage/profile', [
            'name' => 'テストユーザー',
            'postal_code' => '111-1111',
            'address' => '東京都板橋区',
            'building' => '板橋ビル101'
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'username' => 'テストユーザー',
            'postal_code' => '111-1111',
            'address' => '東京都板橋区',
            'building_name' => '板橋ビル101',
        ]);
    }

    public function test_user_can_update_profile_second_time()
    {
        $user = User::factory()->create(['first_login' => false]);
        $this->actingAs($user);

        $response = $this->post('/mypage/profile', [
            'name' => 'リピートユーザー',
            'postal_code' => '111-0000',
            'address' => '東京都北区',
            'building' => '北区ビル101'
        ]);

        $response->assertRedirect(route('mypage.profile.edit'));

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'username' => 'リピートユーザー',
            'postal_code' => '111-0000',
            'address' => '東京都北区',
            'building_name' => '北区ビル101',
        ]);
    }

    public function test_profile_update_requires_validation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/mypage/profile', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }
}