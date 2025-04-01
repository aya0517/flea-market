<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'profile_image' => $this->faker->randomElement([
                'images/profiles/avatar1.png',
                'images/profiles/avatar2.png',
                'images/profiles/avatar3.png',
                'images/profiles/avatar4.png',
                'images/profiles/avatar5.png',
            ]),
            'username' => $this->faker->userName(),
            'postal_code' => $this->faker->postcode(),
            'address' => $this->faker->address(),
            'building_name' => $this->faker->secondaryAddress(),
        ];
    }
}
