<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'type' => fake()->randomElement(['assignment', 'resource', 'quiz']),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(3, true),
            'file_url' => fake()->boolean(60) ? fake()->url() : null,
            'section_order' => fake()->numberBetween(1, 10),
            'is_visible' => fake()->boolean(90),
        ];
    }

    public function assignment(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'assignment',
            'title' => 'Assignment: ' . fake()->sentence(3),
            'file_url' => fake()->url(),
        ]);
    }

    public function resource(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'resource',
            'title' => 'Resource: ' . fake()->sentence(3),
            'file_url' => fake()->url(),
        ]);
    }

    public function quiz(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'quiz',
            'title' => 'Quiz: ' . fake()->sentence(3),
            'file_url' => null,
        ]);
    }
}
