<?php

use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');

Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
	Route::post('/login', [AuthController::class, 'login'])->name('login.store');
	Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
	Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
	Route::get('/dashboard', [BlogController::class, 'dashboard'])->name('dashboard');
	Route::get('/posts/create', [BlogController::class, 'create'])->name('posts.create');
	Route::post('/posts', [BlogController::class, 'store'])->name('posts.store');
	Route::get('/posts/{post}/edit', [BlogController::class, 'edit'])->name('posts.edit');
	Route::patch('/posts/{post}', [BlogController::class, 'update'])->name('posts.update');
	Route::delete('/posts/{post}', [BlogController::class, 'destroy'])->name('posts.destroy');
	Route::get('/comments/{comment}/edit', [BlogController::class, 'editComment'])->name('comments.edit');
	Route::patch('/comments/{comment}', [BlogController::class, 'updateComment'])->name('comments.update');
	Route::delete('/comments/{comment}', [BlogController::class, 'destroyComment'])->name('comments.destroy');
	Route::get('/ratings/{rating}/edit', [BlogController::class, 'editRating'])->name('ratings.edit');
	Route::patch('/ratings/{rating}', [BlogController::class, 'updateRating'])->name('ratings.update');
	Route::delete('/ratings/{rating}', [BlogController::class, 'destroyRating'])->name('ratings.destroy');	
	Route::post('/editor-request', [BlogController::class, 'requestEditor'])->name('editor.request');
	Route::post('/posts/{post}/comments', [BlogController::class, 'storeComment'])->name('posts.comments.store');
	Route::post('/posts/{post}/ratings', [BlogController::class, 'storeRating'])->name('posts.ratings.store');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
	Route::get('/posts/pending', [AdminPostController::class, 'index'])->name('posts.pending');
	Route::patch('/users/{user}/role', [AdminPostController::class, 'updateUserRole'])->name('users.role');
	Route::get('/posts/{post}/preview', [AdminPostController::class, 'preview'])->name('posts.preview');
	Route::patch('/posts/{post}/approve', [AdminPostController::class, 'approve'])->name('posts.approve');
	Route::patch('/posts/{post}/reject', [AdminPostController::class, 'reject'])->name('posts.reject');
});

Route::get('/posts/{post}', [BlogController::class, 'show'])->name('posts.show');
Route::get('/posts/{id}/open', [BlogController::class, 'open'])->name('posts.open');
