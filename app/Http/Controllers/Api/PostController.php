<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Inicio del blog: posts publicados, estadísticas del sitio y top 3.
     * Es pública: un invitado (sin token) la puede ver.
     */
    public function index(): JsonResponse
    {
        $posts = $this->publishedPosts();

        $averageRating = Rating::query()->avg('score');

        return response()->json([
            'posts' => $posts->map(fn (Post $post) => $this->summary($post)),
            'stats' => [
                'published_posts' => $posts->count(),
                'total_comments' => Comment::count(),
                'total_ratings' => Rating::count(),
                'average_rating' => $averageRating ? round((float) $averageRating, 1) : 0,
            ],
            'top' => $this->ranked($posts)->take(3)->values()->map(fn (Post $post) => $this->summary($post)),
        ]);
    }

    /**
     * Ranking completo: todos los posts publicados, del mejor al peor.
     * Mismo criterio que el top 3 del inicio.
     */
    public function ranking(): JsonResponse
    {
        return response()->json([
            'posts' => $this->ranked($this->publishedPosts())->values()->map(fn (Post $post) => $this->summary($post)),
        ]);
    }

    /**
     * Autores: quienes tienen al menos un post publicado, con sus posts.
     * Ordenados por cantidad de posts publicados.
     */
    public function authors(): JsonResponse
    {
        $authors = $this->publishedPosts()
            ->groupBy('user_id')
            ->map(function (Collection $posts) {
                $rated = $posts->filter(fn (Post $post) => $post->ratings_count > 0);

                return [
                    'id' => $posts->first()->user->id,
                    'name' => $posts->first()->user->name,
                    'posts_count' => $posts->count(),
                    'comments_count' => $posts->sum('comments_count'),
                    'rating_avg' => $rated->isNotEmpty() ? round((float) $rated->avg('ratings_avg_score'), 1) : 0,
                    'posts' => $posts->map(fn (Post $post) => $this->summary($post))->values(),
                ];
            })
            ->sortByDesc('posts_count')
            ->values();

        return response()->json(['authors' => $authors]);
    }

    // Posts publicados con autor, contadores y promedio, del más nuevo al más viejo.
    private function publishedPosts(): Collection
    {
        return Post::query()
            ->where('is_published', Post::STATUS_PUBLISHED)
            ->with('user:id,name')
            ->withCount(['comments', 'ratings'])
            ->withAvg('ratings', 'score')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();
    }

    // Ordena por promedio de calificación y, a igual promedio, por comentarios.
    private function ranked(Collection $posts): Collection
    {
        return $posts->sortByDesc(fn (Post $post) => (($post->ratings_avg_score ?? 0) * 1000) + ($post->comments_count ?? 0));
    }

    private function summary(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'excerpt' => Str::limit($post->content, 140),
            'image_url' => $post->image_path ? asset('storage/' . $post->image_path) : null,
            'published_at' => $post->published_at?->toIso8601String(),
            'author' => $post->user->name,
            'comments_count' => $post->comments_count,
            'rating_avg' => round((float) ($post->ratings_avg_score ?? 0), 1),
        ];
    }
}
