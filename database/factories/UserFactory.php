<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'phone' => fake()->numerify('##########'),
            'company' => fake()->company(),
            'user_role' => UserRole::User,
            'status' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['user_role' => UserRole::Admin]);
    }

    public function guest(): static
    {
        return $this->state(fn () => ['user_role' => UserRole::Guest]);
    }

    public function customer(): static
    {
        return $this->state(fn () => [
            'email' => config('digital-meter.customer_email'),
            'user_role' => UserRole::Guest,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => false]);
    }
}
