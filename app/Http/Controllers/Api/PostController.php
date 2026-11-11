<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


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
            'author_id' => $post->user_id,
            'comments_count' => $post->comments_count,
            'rating_avg' => round((float) ($post->ratings_avg_score ?? 0), 1),
        ];
    }
        public function show(Request $request, Post $post): JsonResponse
{
    abort_unless($post->isPublished(), 404);

    $post->load(['user:id,name', 'comments' => fn ($query) => $query->with('user:id,name')->latest()])
        ->loadCount(['comments', 'ratings'])
        ->loadAvg('ratings', 'score');

    $user = $request->user('sanctum');

    return response()->json([
        'post' => [
            ...$this->summary($post),
            'content' => $post->content,
            'ratings_count' => $post->ratings_count,
        ],
        'comments' => $post->comments->map(fn (Comment $comment) => $this->commentData($comment)),
        'my_rating' => $user ? $post->ratings()->where('user_id', $user->id)->value('score') : null,
    ]);
}
// Solo admin y editor crean posts. Quedan pendientes hasta que un admin los apruebe.
public function store(Request $request): JsonResponse
{
    abort_unless($request->user()->canPublish(), 403);

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:180'],
        'content' => ['required', 'string', 'min:20'],
        'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ]);

    $post = Post::create([
        'user_id' => $request->user()->id,
        'title' => $validated['title'],
        'content' => $validated['content'],
        'image_path' => $request->hasFile('image') ? $request->file('image')->store('posts', 'public') : null,
        'is_published' => Post::STATUS_DRAFT,
        'published_at' => null,
    ]);

    return response()->json(['id' => $post->id, 'status' => 'pending'], 201);
}
// Dashboard: los posts del usuario con su estado, métricas y si ya pidió ser editor.
public function mine(Request $request): JsonResponse
{
    $user = $request->user();

    $posts = Post::query()
        ->where('user_id', $user->id)
        ->withCount(['comments', 'ratings'])
        ->withAvg('ratings', 'score')
        ->latest()
        ->get();

    $rated = $posts->filter(fn (Post $post) => $post->ratings_count > 0);

    return response()->json([
        'posts' => $posts->map(fn (Post $post) => [
            'id' => $post->id,
            'title' => $post->title,
            'status' => match (true) {
                $post->isPublished() => 'published',
                $post->isRejected() => 'rejected',
                default => 'pending',
            },
            'rejection_reason' => $post->isRejected() ? $post->rejection_reason : null,
            'published_at' => $post->published_at?->toIso8601String(),
            'created_at' => $post->created_at?->toIso8601String(),
            'comments_count' => $post->comments_count,
            'rating_avg' => round((float) ($post->ratings_avg_score ?? 0), 1),
        ]),
        'stats' => [
            'total_posts' => $posts->count(),
            'published_posts' => $posts->filter(fn (Post $post) => $post->isPublished())->count(),
            'total_comments' => $posts->sum('comments_count'),
            'average_rating' => $rated->isNotEmpty() ? round((float) $rated->avg('ratings_avg_score'), 1) : 0,
        ],
        'requested_editor' => $user->requested_editor_at !== null,
    ]);
}
// El dueño (o un admin) elimina un post y su imagen.
public function destroy(Request $request, Post $post): JsonResponse
{
    abort_unless($request->user()->id === $post->user_id || $request->user()->isAdmin(), 403);

    if ($post->image_path) {
        Storage::disk('public')->delete($post->image_path);
    }

    $post->delete();

    return response()->json(null, 204);
}
// Un usuario común pide el rol de editor; el admin lo ve en moderación.
public function requestEditor(Request $request): JsonResponse
{
    $user = $request->user();
    abort_if($user->canPublish(), 409, 'Ya tenés permisos para publicar.');

    if (! $user->requested_editor_at) {
        $user->update(['requested_editor_at' => now()]);
    }

    return response()->json(['requested' => true]);
}
public function comment(Request $request, Post $post): JsonResponse
{
    abort_unless($post->isPublished(), 404);

    $validated = $request->validate([
        'content' => ['required', 'string', 'min:8', 'max:1000'],
    ]);

    $comment = $post->comments()->create([
        'user_id' => $request->user()->id,
        'content' => $validated['content'],
    ]);

    return response()->json(['comment' => $this->commentData($comment->load('user:id,name'))], 201);
}
public function rate(Request $request, Post $post): JsonResponse
{
    abort_unless($post->isPublished(), 404);

    $validated = $request->validate([
        'score' => ['required', 'integer', 'between:1,5'],
    ]);

    Rating::updateOrCreate(
        ['post_id' => $post->id, 'user_id' => $request->user()->id],
        ['score' => $validated['score']],
    );

    return response()->json(['score' => $validated['score']]);
}
// Solo el dueño del post responde a los comentarios (ni siquiera un admin).
public function reply(Request $request, Comment $comment): JsonResponse
{
    $this->assertPostOwner($request, $comment);

    $validated = $request->validate([
        'reply' => ['required', 'string', 'min:2', 'max:1000'],
    ]);

    $comment->update([
        'reply' => $validated['reply'],
        'reply_at' => now(),
    ]);

    return response()->json(['comment' => $this->commentData($comment->load('user:id,name'))]);
}
// El dueño del post borra comentarios no deseados (la respuesta se va con el comentario).
public function destroyComment(Request $request, Comment $comment): JsonResponse
{
    $this->assertPostOwner($request, $comment, false);

    $comment->delete();

    return response()->json(null, 204);
}
// Solo borra la respuesta del dueño; el comentario queda.
public function destroyReply(Request $request, Comment $comment): JsonResponse
{
    $this->assertPostOwner($request, $comment, false);

    $comment->update(['reply' => null, 'reply_at' => null]);

    return response()->json(['comment' => $this->commentData($comment->load('user:id,name'))]);
}
private function assertPostOwner(Request $request, Comment $comment, bool $requirePublished = true): void
{
    $post = $comment->post;

    abort_unless($request->user()->id === $post->user_id, 403);
    abort_if($requirePublished && ! $post->isPublished(), 404);
}
private function commentData(Comment $comment): array
{
    return [
        'id' => $comment->id,
        'content' => $comment->content,
        'author' => $comment->user->name,
        'created_at' => $comment->created_at?->toIso8601String(),
        'reply' => $comment->reply,
        'reply_at' => $comment->reply_at?->toIso8601String(),
    ];
}

}
