<?php

namespace App\Http\Controllers;

use App\Models\PostOffer;
use App\Models\Review;
use App\Models\User;
use App\PostOfferStatus;
use App\PostStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PostProgressController extends Controller
{
    public function index(): View
    {
        $matches = PostOffer::query()->with(['post', 'user'])->where('status', PostOfferStatus::ACCEPTED)->where(function ($query) {
            $query->where('user_id', Auth::id())->orWhereRelation('post', 'user_id', Auth::id());
        })->latest()->get();

        return view('postprogress.index', compact('matches'));
    }

    public function show(PostOffer $offer): View
    {
        Gate::authorize('view', $offer);

        $offer->load(['post.user', 'user']);

        $myReview = $offer->reviews()->where('reviewer_id', Auth::id())->first();

        return view('postprogress.show', compact('offer', 'myReview'));
    }

    public function complete(Request $request, PostOffer $offer): RedirectResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($offer, $user) {
            $offer = $offer->lockWithPost();

            Gate::forUser($user)->authorize('complete', $offer);

            $offer->completeOffers()->create(['user_id' => $user->id]);

            if ($offer->completeOffers()->count() === 2) {
                $offer->post->status = PostStatus::COMPLETED;
                $offer->post->save();

                // The author picked this partner, so close every other exchange and offer on the post
                $offer->post->offers()
                    ->whereKeyNot($offer->id)
                    ->whereIn('status', [PostOfferStatus::PENDING, PostOfferStatus::ACCEPTED])
                    ->update(['status' => PostOfferStatus::REJECTED]);
            }
        });

        return back();
    }

    /**
     * Agree to cancel an in-progress exchange. Once both participants agree it's closed, and the post
     * opens for offers again if the author has no other exchanges running on it.
     */
    public function cancel(Request $request, PostOffer $offer): RedirectResponse
    {
        $user = $request->user();

        $isCancelled = DB::transaction(function () use ($offer, $user) {
            $offer = $offer->lockWithPost();

            Gate::forUser($user)->authorize('cancelExchange', $offer);

            $offer->cancelOffers()->create(['user_id' => $user->id]);

            if ($offer->cancelOffers()->count() < 2) {
                return false;
            }

            $offer->status = PostOfferStatus::REJECTED;
            $offer->save();

            if ($offer->post->offers()->where('status', PostOfferStatus::ACCEPTED)->doesntExist()) {
                $offer->post->status = PostStatus::AVAILABLE;
                $offer->post->save();
            }

            return true;
        });

        if (! $isCancelled) {
            return back();
        }

        return $offer->post->user_id === $user->id
            ? redirect()->route('posts.offers')
            : redirect()->route('posts.progress');
    }

    public function review(Request $request, PostOffer $offer): RedirectResponse
    {
        $user = $request->user();

        Gate::forUser($user)->authorize('review', $offer);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        $revieweeId = $offer->user_id === $user->id ? $offer->post->user_id : $offer->user_id;

        $offer->reviews()->create([
            'reviewer_id' => $user->id,
            'reviewee_id' => $revieweeId,
            'review' => $validated['rating'],
        ]);

        $average = (float) Review::where('reviewee_id', $revieweeId)->avg('review');

        $reviewee = User::findOrFail($revieweeId);
        $reviewee->reputation = (string) round($average, 2);
        $reviewee->save();

        return back();
    }
}
