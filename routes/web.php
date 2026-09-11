<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\PostOfferController;
use App\Http\Controllers\PostRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('home');

    Route::get('requests', [PostRequestController::class, 'index'])->name('posts.requests');
    Route::get('offers', [PostOfferController::class, 'index'])->name('posts.offers');
    Route::patch('offers/{offer}/accept', [PostOfferController::class, 'accept'])->name('post.offers.accept');

    Route::delete('offers/{offer}', [PostOfferController::class, 'destroy'])->name('posts.offers.cancel');
    Route::delete('offers/{offer}/reject', [PostOfferController::class, 'reject'])->name('posts.offers.reject');
    
    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/create', [PostController::class, 'store'])->name('post.store');
    Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

require __DIR__.'/settings.php';
