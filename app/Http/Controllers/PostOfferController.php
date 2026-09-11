<?php

namespace App\Http\Controllers;

use App\Models\PostOffer;
use App\Policies\PostOfferPolicy;
use App\PostOfferStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostOfferController extends Controller
{
    public function index(): View
    {
        $offers = PostOffer::query()->with(['user', 'post'])->whereRelation('post', 'user_id', Auth::id())->latest()->get();

        return view('postoffer.index', compact('offers'));
    }

    public function destroy(PostOffer $offer): RedirectResponse
    {
        Gate::authorize('cancel', $offer);

        $offer->delete();

        return back();
    }
    public function reject(PostOffer $offer): RedirectResponse
    {
        Gate::authorize('reject', $offer);

        $offer->delete();

        return back();
    }
    public function accept(PostOffer $offer): RedirectResponse
    {
        Gate::authorize('accept', $offer);

        $offer->status = PostOfferStatus::ACCEPTED;
        $offer->save();

        return back();
    }
}
