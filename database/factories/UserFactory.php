<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'birthday' => fake()->date('Y-m-d', '-15 years'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'id_number' => fake()->unique()->numerify('##########'),
            'department' => fake()->randomElement(['IT', 'Science', 'Arts', 'Math', 'Language']),
            'is_active' => fake()->boolean(95),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function teacher(): static
    {
        return $this->state(fn(array $attributes) => [
            'department' => fake()->randomElement(['Math', 'Science', 'Language', 'Arts', 'IT']),
        ]);
    }

    public function student(): static
    {
        return $this->state(fn(array $attributes) => [
            'birthday' => fake()->date('Y-m-d', '-12 years'),
        ]);
    }
}
