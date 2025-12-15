<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');

        return [
            'category_id' => Category::factory(),
            'fullname' => fake()->sentence(3),
            'shortname' => fake()->unique()->slug(2),
            'summary' => fake()->paragraph(),
            'start_date' => $startDate,
            'end_date' => fake()->dateTimeBetween($startDate, '+6 months'),
            'is_visible' => fake()->boolean(85),
            'format' => fake()->randomElement(['topics', 'weeks', 'social']),
            'image_url' => fake()->imageUrl(640, 480, 'education', true),
        ];
    }

    public function visible(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_visible' => true,
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_visible' => false,
        ]);
    }
}
