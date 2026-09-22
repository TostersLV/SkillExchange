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
            $offer = PostOffer::with('post')->lockForUpdate()->findOrFail($offer->id);

            Gate::forUser($user)->authorize('complete', $offer);

            $offer->completeOffers()->create(['user_id' => $user->id]);

            if ($offer->completeOffers()->count() === 2) {
                $offer->post->status = PostStatus::COMPLETED;
                $offer->post->save();
            }
        });

        return back();
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
