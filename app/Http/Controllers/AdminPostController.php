<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class AdminPostController extends Controller
{
    private const ROLES = ['admin', 'editor', 'user'];

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

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $editorRequests = User::query()
            ->whereNotNull('requested_editor_at')
            ->where('role', 'user')
            ->orderBy('requested_editor_at')
            ->get(['id', 'name', 'requested_editor_at']);

        return view('admin.posts.pending', [
            'posts'          => $posts,
            'users'          => $users,
            'roles'          => self::ROLES,
            'user'           => Auth::user(),
            'editorRequests' => $editorRequests,
        ]);
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $this->assertAdmin();

        if (Auth::id() === $user->id) {
            return redirect()
                ->route('admin.posts.pending')
                ->with('status', 'No puedes cambiar tu propio rol desde moderacion.');
        }

        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(self::ROLES)],
        ]);

        $user->update([
            'role'                => $validated['role'],
            'requested_editor_at' => null,
        ]);

        return redirect()
            ->route('admin.posts.pending')
            ->with('status', 'Rol de usuario actualizado.');
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

    public function reject(Request $request, Post $post): RedirectResponse
    {
        $this->assertAdmin();

        if ((int) $post->is_published !== Post::STATUS_DRAFT) {
            return redirect()
                ->route('admin.posts.pending')
                ->with('status', 'El post ya fue resuelto.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $post->update([
            'is_published' => Post::STATUS_REJECTED,
            'published_at' => null,
            'rejection_reason' => $validated['rejection_reason'],
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
