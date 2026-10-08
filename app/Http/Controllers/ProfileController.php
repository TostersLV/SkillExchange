<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\PostStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        $posts = $user->posts()
            ->with('category')
            ->when($user->isNot(Auth::user()), fn ($query) => $query->where('status', PostStatus::AVAILABLE))
            ->latest()
            ->get();

        return view('profile.show', [
            'user' => $user,
            'posts' => $posts,
            'closedOffers' => Auth::user()->closedOfferStatusesByPost(),
            'reviewsCount' => $user->reviewsReceived()->count(),
            'completedExchangesCount' => $user->completedExchangesCount(),
        ]);
    }
}
