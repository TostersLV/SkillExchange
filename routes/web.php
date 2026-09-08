<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('home');

    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/create', [PostController::class, 'store'])->name('post.store');
    Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
});

require __DIR__.'/settings.php';
