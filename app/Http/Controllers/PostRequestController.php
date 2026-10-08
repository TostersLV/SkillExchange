<?php

namespace App\Http\Controllers;

use App\Models\PostOffer;
use App\PostOfferStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class PostRequestController extends Controller
{
    public function index(): View
    {
        // Pending offers, plus declined or cancelled ones until the sender dismisses them
        $offers = PostOffer::query()
            ->with('post.user')
            ->withCount('cancelOffers')
            ->where('user_id', Auth::id())
            ->where(fn ($query) => $query
                ->where('status', PostOfferStatus::PENDING)
                ->orWhere(fn ($query) => $query->whereIn('status', PostOfferStatus::closed())->whereNull('dismissed_at')))
            ->latest('updated_at')
            ->get();

        return view('postsrequest.index', compact('offers'));
    }
}
