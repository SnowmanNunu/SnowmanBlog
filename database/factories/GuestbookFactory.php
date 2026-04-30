<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Guestbook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guestbook>
 */
class GuestbookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nickname' => fake()->name(),
            'email' => fake()->safeEmail(),
            'website' => fake()->optional()->url(),
            'content' => fake()->paragraph(),
            'reply' => null,
            'replied_at' => null,
            'is_approved' => true,
            'ip' => fake()->ipv4(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }

    public function replied(): static
    {
        return $this->state(fn (array $attributes) => [
            'reply' => fake()->paragraph(),
            'replied_at' => now(),
        ]);
    }
}
