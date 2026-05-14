<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminPostController extends Controller
{
    public function preview(Post $post): View
    {
        $this->assertAdmin();

        if ((int) $post->is_published !== Post::STATUS_DRAFT) {
            abort(404);
        }

        return view('admin.posts.preview', [
            'post' => $post->load('user:id,name'),
            'user' => Auth::user(),
        ]);
    }

    public function index(): View
    {
        $this->assertAdmin();

        $posts = Post::query()
            ->where('is_published', Post::STATUS_DRAFT)
            ->with('user:id,name')
            ->latest('created_at')
            ->get();

        return view('admin.posts.pending', [
            'posts' => $posts,
            'user' => Auth::user(),
        ]);
    }

    public function approve(Post $post): RedirectResponse
    {
        $this->assertAdmin();

        if ((int) $post->is_published !== Post::STATUS_DRAFT) {
            return redirect()
                ->route('admin.posts.pending')
                ->with('status', 'El post ya no está pendiente.');
        }

        $post->update([
            'is_published' => Post::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.posts.pending')
            ->with('status', 'Post aprobado y publicado.');
    }

    public function reject(Post $post): RedirectResponse
    {
        $this->assertAdmin();

        if ((int) $post->is_published !== Post::STATUS_DRAFT) {
            return redirect()
                ->route('admin.posts.pending')
                ->with('status', 'El post ya fue resuelto.');
        }

        $post->update([
            'is_published' => Post::STATUS_REJECTED,
            'published_at' => null,
        ]);

        return redirect()
            ->route('admin.posts.pending')
            ->with('status', 'Post rechazado.');
    }

    private function assertAdmin(): void
    {
        $user = Auth::user();

        abort_unless($user && $user->role === 'admin', 403);
    }
}
