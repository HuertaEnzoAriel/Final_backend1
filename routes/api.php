<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Públicas (invitados incluidos).
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/ranking', [PostController::class, 'ranking']);
Route::get('/posts/{post}', [PostController::class, 'show'])->whereNumber('post');

Route::get('/authors', [PostController::class, 'authors']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Requieren token.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/posts/{post}/comments', [PostController::class, 'comment']);
    Route::post('/posts/{post}/ratings', [PostController::class, 'rate']);
    Route::post('/comments/{comment}/reply', [PostController::class, 'reply'])->whereNumber('comment');
    Route::delete('/comments/{comment}/reply', [PostController::class, 'destroyReply'])->whereNumber('comment');
    Route::delete('/comments/{comment}', [PostController::class, 'destroyComment'])->whereNumber('comment');

    Route::get('/me/activity', [ActivityController::class, 'index']);
    Route::get('/me/activity/summary', [ActivityController::class, 'summary']);
    Route::post('/me/activity/seen', [ActivityController::class, 'seen']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
