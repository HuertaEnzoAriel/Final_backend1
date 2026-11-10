<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ActivityController extends Controller
{
    private const PAGE_SIZE = 20;

    /** Comentarios de otros usuarios sobre posts del usuario autenticado. */
    private function commentsQuery(int $me)
    {
        return Comment::query()
            ->whereHas('post', fn ($q) => $q->where('user_id', $me))
            ->where('user_id', '!=', $me);
    }

    /** Calificaciones de otros usuarios sobre posts del usuario autenticado. */
    private function ratingsQuery(int $me)
    {
        return Rating::query()
            ->whereHas('post', fn ($q) => $q->where('user_id', $me))
            ->where('user_id', '!=', $me);
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'nullable|in:all,comments,ratings',
            'post_id' => 'nullable|integer',
            'before' => 'nullable|date',
        ]);

        $user = $request->user();
        $type = $request->query('type', 'all');
        $postId = $request->query('post_id');
        $before = $request->query('before') ? Carbon::parse($request->query('before')) : null;
        $seen = $user->activity_seen_at;
        $limit = self::PAGE_SIZE;

        $items = collect();

        if ($type !== 'ratings') {
            $query = $this->commentsQuery($user->id)->with('user:id,name', 'post:id,title');
            if ($postId) {
                $query->where('post_id', $postId);
            }
            if ($before) {
                $query->where('created_at', '<', $before);
            }
            foreach ($query->latest()->limit($limit + 1)->get() as $c) {
                $items->push([
                    'type' => 'comment',
                    'id' => $c->id,
                    'post_id' => $c->post_id,
                    'post_title' => $c->post?->title,
                    'author' => $c->user?->name,
                    'content' => $c->content,
                    'score' => null,
                    'created_at' => $c->created_at->toISOString(),
                    'is_new' => $seen === null || $c->created_at->gt($seen),
                ]);
            }
        }

        if ($type !== 'comments') {
            $query = $this->ratingsQuery($user->id)->with('user:id,name', 'post:id,title');
            if ($postId) {
                $query->where('post_id', $postId);
            }
            if ($before) {
                $query->where('updated_at', '<', $before);
            }
            foreach ($query->orderByDesc('updated_at')->limit($limit + 1)->get() as $r) {
                $items->push([
                    'type' => 'rating',
                    'id' => $r->id,
                    'post_id' => $r->post_id,
                    'post_title' => $r->post?->title,
                    'author' => $r->user?->name,
                    'content' => null,
                    'score' => $r->score,
                    'created_at' => $r->updated_at->toISOString(),
                    'is_new' => $seen === null || $r->updated_at->gt($seen),
                ]);
            }
        }

        $sorted = $items->sortByDesc('created_at')->values();
        $hasMore = $sorted->count() > $limit;
        $page = $sorted->take($limit)->values();

        return response()->json([
            'data' => $page,
            'next_before' => $hasMore ? $page->last()['created_at'] : null,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $seen = $user->activity_seen_at;

        $comments = $this->commentsQuery($user->id);
        $ratings = $this->ratingsQuery($user->id);

        $unreadComments = (clone $comments)->when($seen, fn ($q) => $q->where('created_at', '>', $seen))->count();
        $unreadRatings = (clone $ratings)->when($seen, fn ($q) => $q->where('updated_at', '>', $seen))->count();

        return response()->json([
            'unread' => $unreadComments + $unreadRatings,
            'comments_total' => $comments->count(),
            'ratings_total' => $ratings->count(),
            'average' => round((float) $ratings->avg('score'), 1),
        ]);
    }

    public function seen(Request $request): JsonResponse
    {
        $request->user()->forceFill(['activity_seen_at' => Carbon::now()])->save();

        return response()->json(['ok' => true]);
    }
}
