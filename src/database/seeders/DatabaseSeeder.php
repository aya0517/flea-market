<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
{
    \App\Models\User::factory(10)->create();

    User::create([
        'name' => 'user',
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'is_admin' => false,
    ]);

    User::create([
        'name' => 'admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'is_admin' => true,
    ]);

    $this->call([
        CategorySeeder::class,
        ConditionSeeder::class,
        ItemSeeder::class,
    ]);
}

}
