<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $published = fake()->boolean(85);

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(6),
            'content' => fake()->paragraphs(4, true),
            'image_path' => fake()->optional(0.35)->imageUrl(1280, 720, 'business', true),
            'is_published' => $published,
            'published_at' => $published ? fake()->dateTimeBetween('-90 days', 'now') : null,
        ];
    }
}
