<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $posts = [
            [
                'title' => 'Como organizar un blog pequeño sin perder claridad',
                'content' => 'Una estructura simple de modelos, relaciones y seeders te permite avanzar rapido sin sacrificar mantenimiento.',
                'image_path' => null,
                'is_published' => Post::STATUS_PUBLISHED,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Laravel para proyectos academicos: lo justo y necesario',
                'content' => 'Cuando el objetivo es aprender bien, conviene modelar poco, pero con relaciones solidas y datos faciles de recrear.',
                'image_path' => null,
                'is_published' => Post::STATUS_PUBLISHED,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Seeders utiles para demostrar funcionalidades',
                'content' => 'Un buen set de datos inicial ayuda a probar vistas, filtros y relaciones sin depender de captura manual.',
                'image_path' => null,
                'is_published' => Post::STATUS_PUBLISHED,
                'published_at' => now()->subDay(),
            ],
            [
                'title' => 'Detalles visuales que mejoran la lectura del contenido',
                'content' => 'Aunque la app sea simple, un frontend limpio hace mas claro el recorrido de lectura y navegacion.',
                'image_path' => null,
                'is_published' => Post::STATUS_REJECTED,
                'published_at' => null,
            ],
        ];

        foreach ($posts as $index => $post) {
            Post::updateOrCreate(
                ['title' => $post['title']],
                [
                    'user_id' => $users[$index % $users->count()]->id,
                    'content' => $post['content'],
                    'image_path' => $post['image_path'],
                    'is_published' => $post['is_published'],
                    'published_at' => $post['published_at'],
                ]
            );
        }
    }
}
