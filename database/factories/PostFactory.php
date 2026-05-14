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
        $status = fake()->randomElement([
            Post::STATUS_DRAFT,
            Post::STATUS_PUBLISHED,
            Post::STATUS_REJECTED,
        ]);

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(6),
            'content' => fake()->paragraphs(4, true),
            'image_path' => fake()->optional(0.35)->imageUrl(1280, 720, 'business', true),
            'is_published' => $status,
            'published_at' => $status === Post::STATUS_PUBLISHED
                ? fake()->dateTimeBetween('-90 days', 'now')
                : null,
        ];
    }
}
