<?php

namespace Database\Factories;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Place>
 */
class PlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['attraction', 'restaurant']),
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'category' => fake()->word(),
            'area' => fake()->city(),
            'address' => fake()->address(),
            'description' => fake()->paragraph(),
            'image_path' => '/images/kuala-lumpur-skyline.jpg',
            'rating' => fake()->randomFloat(1, 3, 5),
            'review_count' => fake()->numberBetween(10, 10000),
            'price_level' => fake()->numberBetween(1, 4),
            'tags' => fake()->randomElements(['culture', 'family', 'local food', 'outdoor'], 2),
            'latitude' => fake()->latitude(3.0, 3.3),
            'longitude' => fake()->longitude(101.6, 101.8),
        ];
    }
}
