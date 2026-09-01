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

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            // era fake()->boolean(): il ruolo veniva estratto a caso, quindi i
            // test e la demo non sapevano mai chi fosse docente
            'isTeacher' => false,
            'remember_token' => Str::random(10),
        ];
    }

    public function teacher(): static
    {
        return $this->state(fn () => ['isTeacher' => true]);
    }

    public function student(): static
    {
        return $this->state(fn () => ['isTeacher' => false]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
