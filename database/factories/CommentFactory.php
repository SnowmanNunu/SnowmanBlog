<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'parent_id' => null,
            'nickname' => fake()->name(),
            'email' => fake()->safeEmail(),
            'website' => fake()->optional()->url(),
            'content' => fake()->paragraph(),
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
}
