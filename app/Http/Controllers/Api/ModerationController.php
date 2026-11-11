<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Moderación de posts: solo admin. Los pendientes son los que están en borrador.
class ModerationController extends Controller
{
    private const ROLES = ['admin', 'editor', 'user'];

    public function index(Request $request): JsonResponse
    {
        $this->assertAdmin($request);

        $posts = Post::query()
            ->where('is_published', Post::STATUS_DRAFT)
            ->with('user:id,name')
            ->oldest()
            ->get()
            ->map(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'image_url' => $post->image_path ? asset('storage/' . $post->image_path) : null,
                'author' => $post->user->name,
                'created_at' => $post->created_at?->toIso8601String(),
            ]);

        return response()->json(['posts' => $posts]);
    }

    public function approve(Request $request, Post $post): JsonResponse
    {
        $this->assertAdmin($request);
        abort_unless($post->isDraft(), 409, 'El post ya no está pendiente.');

        $post->update([
            'is_published' => Post::STATUS_PUBLISHED,
            'published_at' => now(),
            'rejection_reason' => null,
        ]);

        Notice::create([
            'user_id' => $post->user_id,
            'kind' => Notice::POST_APPROVED,
            'post_id' => $post->id,
            'title' => $post->title,
        ]);

        return response()->json(['id' => $post->id, 'status' => 'published']);
    }

    public function reject(Request $request, Post $post): JsonResponse
    {
        $this->assertAdmin($request);
        abort_unless($post->isDraft(), 409, 'El post ya fue resuelto.');

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $post->update([
            'is_published' => Post::STATUS_REJECTED,
            'published_at' => null,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        Notice::create([
            'user_id' => $post->user_id,
            'kind' => Notice::POST_REJECTED,
            'post_id' => $post->id,
            'title' => $post->title,
            'detail' => $validated['rejection_reason'],
        ]);

        return response()->json(['id' => $post->id, 'status' => 'rejected']);
    }

    // Usuarios con su rol; primero los que pidieron ser editor.
    public function users(Request $request): JsonResponse
    {
        $this->assertAdmin($request);

        $users = User::query()
            ->orderByRaw('requested_editor_at IS NULL')
            ->orderBy('requested_editor_at')
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'requested_editor' => $user->requested_editor_at !== null && $user->role === 'user',
            ]);

        return response()->json(['users' => $users]);
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $this->assertAdmin($request);
        abort_if($request->user()->id === $user->id, 403, 'No podés cambiar tu propio rol.');

        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(self::ROLES)],
        ]);

        $changed = $user->role !== $validated['role'];

        $user->update([
            'role' => $validated['role'],
            'requested_editor_at' => null,
        ]);

        if ($changed) {
            Notice::create([
                'user_id' => $user->id,
                'kind' => Notice::ROLE_CHANGED,
                'detail' => $validated['role'],
            ]);
        }

        return response()->json(['id' => $user->id, 'role' => $user->role]);
    }

    private function assertAdmin(Request $request): void
    {
        abort_unless($request->user()->isAdmin(), 403);
    }
}
