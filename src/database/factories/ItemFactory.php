<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(100, 10000),
            'description' => $this->faker->sentence(),
            'image_path' => 'images/dummy.jpg',
            'condition_id' => $this->faker->numberBetween(1, 4),
            'user_id' => \App\Models\User::factory(),
            'is_sold' => false,
        ];
    }
}

