<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Khối 6',
                'Khối 7',
                'Khối 8',
                'Khối 9',
                'Toán học',
                'Ngữ văn',
                'Tiếng Anh',
                'Khoa học',
                'Lịch sử',
                'Địa lý',
                'Công nghệ'
            ]),
            'parent_id' => null,
            'is_visible' => fake()->boolean(90),
            'description' => fake()->sentence(),
        ];
    }

    public function withParent(): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => Category::factory(),
        ]);
    }
}
