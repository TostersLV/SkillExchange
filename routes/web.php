<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/', 'posts.index')->name('home');

    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/create', [PostController::class, 'store'])->name('post.store');   
});

require __DIR__.'/settings.php';
