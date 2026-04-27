<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();

        $posts = Post::query()
            ->where('user_id', $user->id)
            ->withCount(['comments', 'ratings'])
            ->withAvg('ratings', 'score')
            ->latest('published_at')
            ->latest('created_at')
            ->get();

        return view('dashboard', [
            'user' => $user,
            'posts' => $posts,
            'totalPosts' => $posts->count(),
            'publishedPosts' => $posts->where('is_published', true)->count(),
            'totalComments' => $posts->sum('comments_count'),
            'averageRating' => $posts->avg('ratings_avg_score') ? round((float) $posts->avg('ratings_avg_score'), 1) : 0,
        ]);
    }

    public function create(): View
    {
        return view('posts.create', [
            'user' => Auth::user(),
        ]);
    }

    public function edit(Post $post): View
    {
        $this->assertCanManageOwner($post->user_id);

        return view('posts.edit', [
            'post' => $post,
        ]);
    }

    public function index(): View
    {
        return $this->renderBlogPage();
    }

    public function show(Post $post): View
    {
        abort_unless($post->is_published, 404);

        return $this->renderBlogPage($post);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'content' => ['required', 'string', 'min:20'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('posts', 'public')
            : null;

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'is_published' => true,
            'published_at' => now(),
        ]);

        return redirect()->route('posts.show', $post)->with('status', 'Post publicado con tu usuario.');
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->assertCanManageOwner($post->user_id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'content' => ['required', 'string', 'min:20'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $imagePath = $post->image_path;

        if ($request->boolean('remove_image') && $imagePath) {
            Storage::disk('public')->delete($imagePath);
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'is_published' => (bool) ($validated['is_published'] ?? true),
            'published_at' => (bool) ($validated['is_published'] ?? true)
                ? ($post->published_at ?? now())
                : null,
        ]);

        return redirect()->route('posts.show', $post)->with('status', 'Post actualizado correctamente.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->assertCanManageOwner($post->user_id);

        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return redirect()->route('dashboard')->with('status', 'Post eliminado correctamente.');
    }

    public function storeComment(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->is_published, 404);

        $rules = [
            'content' => ['required', 'string', 'min:8', 'max:1000'],
        ];

        if (! Auth::check()) {
            $rules['user_id'] = ['required', 'exists:users,id'];
        }

        $validated = $request->validate($rules);

        $userId = Auth::id() ?? $request->integer('user_id');

        if (! $userId) {
            abort(403);
        }

        Comment::create([
            'post_id' => $post->id,
            'user_id' => $userId,
            'content' => $validated['content'],
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Comentario publicado correctamente.');
    }

    public function editComment(Comment $comment): View
    {
        $this->assertCanManageOwner($comment->user_id);

        return view('comments.edit', [
            'comment' => $comment->load('post:id,title'),
        ]);
    }

    public function updateComment(Request $request, Comment $comment): RedirectResponse
    {
        $this->assertCanManageOwner($comment->user_id);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:8', 'max:1000'],
        ]);

        $comment->update([
            'content' => $validated['content'],
        ]);

        return redirect()->route('posts.show', $comment->post_id)->with('status', 'Comentario actualizado.');
    }

    public function destroyComment(Comment $comment): RedirectResponse
    {
        $this->assertCanManageOwner($comment->user_id);

        $postId = $comment->post_id;
        $comment->delete();

        return redirect()->route('posts.show', $postId)->with('status', 'Comentario eliminado.');
    }

    public function storeRating(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->is_published, 404);

        $rules = [
            'score' => ['required', 'integer', 'between:1,5'],
        ];

        if (! Auth::check()) {
            $rules['user_id'] = ['required', 'exists:users,id'];
        }

        $validated = $request->validate($rules);

        $userId = Auth::id() ?? $request->integer('user_id');

        if (! $userId) {
            abort(403);
        }

        Rating::updateOrCreate(
            [
                'post_id' => $post->id,
                'user_id' => $userId,
            ],
            [
                'score' => $validated['score'],
            ]
        );

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Calificacion guardada correctamente.');
    }

    public function editRating(Rating $rating): View
    {
        $this->assertCanManageOwner($rating->user_id);

        return view('ratings.edit', [
            'rating' => $rating->load('post:id,title'),
        ]);
    }

    public function updateRating(Request $request, Rating $rating): RedirectResponse
    {
        $this->assertCanManageOwner($rating->user_id);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'between:1,5'],
        ]);

        $rating->update([
            'score' => $validated['score'],
        ]);

        return redirect()->route('posts.show', $rating->post_id)->with('status', 'Rating actualizado.');
    }

    public function destroyRating(Rating $rating): RedirectResponse
    {
        $this->assertCanManageOwner($rating->user_id);

        $postId = $rating->post_id;
        $rating->delete();

        return redirect()->route('posts.show', $postId)->with('status', 'Rating eliminado.');
    }

    private function renderBlogPage(?Post $selectedPost = null): View
    {
        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $posts = Post::query()
            ->where('is_published', true)
            ->with([
                'user:id,name',
                'comments.user:id,name',
                'ratings.user:id,name',
            ])
            ->withCount(['comments', 'ratings'])
            ->withAvg('ratings', 'score')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();

        $selectedPost = $selectedPost
            ? $selectedPost->load([
                'user:id,name',
                'comments.user:id,name',
                'ratings.user:id,name',
            ])->loadCount(['comments', 'ratings'])->loadAvg('ratings', 'score')
            : null;

        $comments = $selectedPost
            ? $selectedPost->comments()->with('user:id,name')->latest()->get()
            : collect();

        $currentUser = Auth::user();

        $currentUserRating = ($selectedPost && $currentUser)
            ? $selectedPost->ratings()->where('user_id', $currentUser->id)->first()
            : null;

        $highlight = $selectedPost ?? $posts->first();

        $siteStats = [
            'published_posts' => $posts->count(),
            'total_comments' => Comment::count(),
            'total_ratings' => Rating::count(),
            'average_rating' => Rating::query()->avg('score') ? round((float) Rating::query()->avg('score'), 1) : 0,
        ];

        $topPosts = $posts
            ->sortByDesc(fn (Post $post) => (($post->ratings_avg_score ?? 0) * 1000) + ($post->comments_count ?? 0))
            ->take(3)
            ->values();

        return view('blog', [
            'users' => $users,
            'posts' => $posts,
            'selectedPost' => $selectedPost,
            'highlight' => $highlight,
            'comments' => $comments,
            'siteStats' => $siteStats,
            'topPosts' => $topPosts,
            'currentUser' => $currentUser,
            'currentUserRating' => $currentUserRating,
        ]);
    }

    private function assertCanManageOwner(int $ownerId): void
    {
        $user = Auth::user();

        abort_unless(
            $user && ($user->id === $ownerId || $user->role === 'admin'),
            403
        );
    }
}
