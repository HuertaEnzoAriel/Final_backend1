<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private const MESSAGES = [
        'name.required' => 'Ingresá tu nombre.',
        'name.max' => 'El nombre no puede superar los 255 caracteres.',
        'email.required' => 'Ingresá tu email.',
        'email.email' => 'El email no es válido.',
        'email.max' => 'El email no puede superar los 255 caracteres.',
        'email.unique' => 'Ya existe una cuenta con ese email.',
        'password.required' => 'Ingresá tu contraseña.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
    ];

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], self::MESSAGES);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        return response()->json([
            'user' => $this->userData($user),
            'token' => $user->createToken('front')->plainTextToken,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], self::MESSAGES);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        return response()->json([
            'user' => $this->userData($user),
            'token' => $user->createToken('front')->plainTextToken,
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
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
private function commentData(Comment $comment): array
{
    return [
        'id' => $comment->id,
        'content' => $comment->content,
        'author' => $comment->user->name,
        'created_at' => $comment->created_at?->toIso8601String(),
    ];
}

}
