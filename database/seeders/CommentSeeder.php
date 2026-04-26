<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
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

        $comments = [
            'Me sirvio bastante para ordenar la idea.',
            'Buen enfoque, directo y facil de seguir.',
            'Esto ayuda mucho para no sobrecomplicar el proyecto.',
            'Quedo claro el proceso y el orden de trabajo.',
            'Me gusto la forma en que lo explicaste.',
        ];

        foreach ($posts as $postIndex => $post) {
            foreach (range(1, 3) as $offset) {
                $user = $users[($postIndex + $offset) % $users->count()];

                Comment::updateOrCreate(
                    [
                        'post_id' => $post->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'content' => $comments[($postIndex + $offset) % count($comments)],
                    ]
                );
            }
        }
    }
}
