<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Rating;
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
        $posts = Post::query()
            ->where('is_published', Post::STATUS_PUBLISHED)
            ->with('user:id,name')
            ->withCount(['comments', 'ratings'])
            ->withAvg('ratings', 'score')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();

        $averageRating = Rating::query()->avg('score');

        $topPosts = $posts
            ->sortByDesc(fn (Post $post) => (($post->ratings_avg_score ?? 0) * 1000) + ($post->comments_count ?? 0))
            ->take(3)
            ->values();

        return response()->json([
            'posts' => $posts->map(fn (Post $post) => $this->summary($post)),
            'stats' => [
                'published_posts' => $posts->count(),
                'total_comments' => Comment::count(),
                'total_ratings' => Rating::count(),
                'average_rating' => $averageRating ? round((float) $averageRating, 1) : 0,
            ],
            'top' => $topPosts->map(fn (Post $post) => $this->summary($post)),
        ]);
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
