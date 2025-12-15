<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Manager', 'Teacher', 'Student']),
            'shortname' => fake()->unique()->word(),
            'description' => fake()->sentence(),
        ];
    }

    public function manager(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Manager',
            'shortname' => 'manager',
            'description' => 'Can manage courses and users',
        ]);
    }

    public function teacher(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Teacher',
            'shortname' => 'teacher',
            'description' => 'Can teach courses and grade students',
        ]);
    }

    public function student(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Student',
            'shortname' => 'student',
            'description' => 'Can enroll in courses and submit assignments',
        ]);
    }
}
