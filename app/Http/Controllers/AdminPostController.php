<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminPostController extends Controller
{
    public function index(): View
    {
        $this->assertAdmin();

        $posts = Post::query()
            ->where('is_published', false)
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

        if ($post->is_published) {
            return redirect()
                ->route('admin.posts.pending')
                ->with('status', 'El post ya estaba publicado.');
        }

        $post->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.posts.pending')
            ->with('status', 'Post aprobado y publicado.');
    }

    private function assertAdmin(): void
    {
        $user = Auth::user();

        abort_unless($user && $user->role === 'admin', 403);
    }
}
