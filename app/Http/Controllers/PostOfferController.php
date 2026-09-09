<?php

namespace App\Http\Controllers;

use App\Models\PostOffer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostOfferController extends Controller
{
    /**
     * Offers other users have sent for the current user's posts.
     */
    public function index(): View
    {
        $offers = PostOffer::query()
            ->with(['user', 'post'])
            ->whereRelation('post', 'user_id', Auth::id())
            ->latest()
            ->get();

        return view('postoffer.index', compact('offers'));
    }

    /**
     * Cancel an offer the current user sent.
     */
    public function destroy(PostOffer $offer): RedirectResponse
    {
        Gate::authorize('cancel', $offer);

        $offer->delete();

        return back();
    }
}
