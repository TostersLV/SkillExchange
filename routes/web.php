<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\PostOfferController;
use App\Http\Controllers\PostRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('home');

    Route::get('requests', [PostRequestController::class, 'index'])->name('posts.requests');
    Route::get('offers', [PostOfferController::class, 'index'])->name('posts.offers');
    Route::delete('offers/{offer}', [PostOfferController::class, 'destroy'])->name('posts.offers.cancel');

    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/create', [PostController::class, 'store'])->name('post.store');
    Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
});

require __DIR__.'/settings.php';
