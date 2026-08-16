<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Factories;

use Domain\User\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Infrastructure\User\Persistence\Models\UserModel;

/**
 * @extends Factory<UserModel>
 */
class UserModelFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * The model that the factory corresponds to.
     *
     * @var class-string<UserModel>
     */
    protected $model = UserModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'status' => UserStatus::Active,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}
