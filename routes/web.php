<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\PostOfferController;
use App\Http\Controllers\PostProgressController;
use App\Http\Controllers\PostRequestController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('home');

    Route::get('users/{user}', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('requests', [PostRequestController::class, 'index'])->name('posts.requests');
    Route::get('offers', [PostOfferController::class, 'index'])->name('posts.offers');
    Route::get('progress', [PostProgressController::class, 'index'])->name('posts.progress');
    Route::get('progress/{offer}', [PostProgressController::class, 'show'])->name('posts.progress.show');
    Route::patch('progress/{offer}/complete', [PostProgressController::class, 'complete'])->name('posts.progress.complete');
    Route::patch('progress/{offer}/review', [PostProgressController::class, 'review'])->name('posts.progress.review');
    Route::patch('offers/{offer}/accept', [PostOfferController::class, 'accept'])->name('post.offers.accept');

    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/create', [PostController::class, 'store'])->name('post.store');
    Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::delete('offers/{offer}', [PostOfferController::class, 'destroy'])->name('posts.offers.cancel');
    Route::delete('offers/{offer}/reject', [PostOfferController::class, 'reject'])->name('posts.offers.reject');
});

require __DIR__.'/settings.php';
