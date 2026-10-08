<?php

namespace App\Http\Controllers;

use App\Models\PostOffer;
use App\PostOfferStatus;
use App\PostStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PostOfferController extends Controller
{
    public function index(): View
    {
        $offers = PostOffer::query()->with(['user', 'post'])->whereRelation('post', 'user_id', Auth::id())->where('status', PostOfferStatus::PENDING)->latest()->get();

        return view('postoffer.index', compact('offers'));
    }

    public function destroy(PostOffer $offer): RedirectResponse
    {
        Gate::authorize('cancel', $offer);

        $offer->status = PostOfferStatus::WITHDRAWN;
        $offer->save();

        return back();
    }

    public function reject(PostOffer $offer): RedirectResponse
    {
        Gate::authorize('reject', $offer);

        $offer->status = PostOfferStatus::REJECTED;
        $offer->save();

        return back();
    }

    /**
     * Hide a declined or cancelled offer from the sender's requests list. The record itself is kept.
     */
    public function dismiss(PostOffer $offer): RedirectResponse
    {
        Gate::authorize('dismiss', $offer);

        $offer->dismissed_at = now();
        $offer->save();

        return back();
    }

    public function accept(PostOffer $offer): RedirectResponse
    {
        DB::transaction(function () use ($offer) {
            $offer = $offer->lockWithPost();

            Gate::authorize('accept', $offer);

            $offer->status = PostOfferStatus::ACCEPTED;
            $offer->post->status = PostStatus::IN_PROGRESS;
            $offer->save();
            $offer->post->save();
        });

        return back();
    }
}
