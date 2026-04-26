<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $posts = Post::all();

        if ($users->isEmpty() || $posts->isEmpty()) {
            return;
        }

        foreach ($posts as $postIndex => $post) {
            $ratingUsers = $users->slice(0, min(4, $users->count()));

            foreach ($ratingUsers as $userIndex => $user) {
                Rating::updateOrCreate(
                    [
                        'post_id' => $post->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'score' => (($postIndex + $userIndex) % 5) + 1,
                    ]
                );
            }
        }
    }
}
