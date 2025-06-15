<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Domain\Enums\RoleEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Models\User>
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement(RoleEnum::values()),
        ];
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

    /**
     * Ustawia użytkownikowi konkretną rolę.
     */
    public function withRole(RoleEnum $role): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
        ]);
    }

    /** @see RoleEnum::SCIENTIFIC_WORKER */
    public function scientificWorker(): static
    {
        return $this->withRole(RoleEnum::SCIENTIFIC_WORKER);
    }

    /** @see RoleEnum::DEAN_OFFICE_WORKER */
    public function deanOfficeWorker(): static
    {
        return $this->withRole(RoleEnum::DEAN_OFFICE_WORKER);
    }

    /** @see RoleEnum::ADMINISTRATOR */
    public function administrator(): static
    {
        return $this->withRole(RoleEnum::ADMINISTRATOR);
    }
}
