<?php

namespace App\Policies;

use App\Models\PostOffer;
use App\Models\User;
use App\PostOfferStatus;
use App\PostStatus;

class PostOfferPolicy
{
    // Only the two users in an accepted offer can view its progress page
    public function view(User $user, PostOffer $offer): bool
    {
        return $offer->status === PostOfferStatus::ACCEPTED
            && ($user->id === $offer->user_id || $user->id === $offer->post->user_id);
    }

    // Either participant can confirm completion once, while the swap is in progress
    public function complete(User $user, PostOffer $offer): bool
    {
        return $this->view($user, $offer)
            && $offer->post->status === PostStatus::IN_PROGRESS
            && ! $offer->hasBeenCompletedBy($user);
    }

    // Either participant can review the other once, after the exchange is complete
    public function review(User $user, PostOffer $offer): bool
    {
        return $this->view($user, $offer)
            && $offer->post->status === PostStatus::COMPLETED
            && ! $offer->hasBeenReviewedBy($user);
    }

    // Either participant can agree once to cancel the exchange while it's in progress; it's called off when both have agreed
    public function cancelExchange(User $user, PostOffer $offer): bool
    {
        return $this->view($user, $offer)
            && $offer->post->status === PostStatus::IN_PROGRESS
            && ! $offer->hasRequestedCancelBy($user);
    }

    // Only the user who sent an offer can cancel the post
    public function cancel(User $user, PostOffer $offer): bool
    {
        return $user->id === $offer->user_id && $offer->status === PostOfferStatus::PENDING;
    }

    // The sender can hide a declined or cancelled offer from their requests list
    public function dismiss(User $user, PostOffer $offer): bool
    {
        return $user->id === $offer->user_id && $offer->status->isClosed() && $offer->dismissed_at === null;
    }

    // The author can rejected an offer
    public function reject(User $user, PostOffer $offer): bool
    {
        return $user->id === $offer->post->user_id && $offer->status === PostOfferStatus::PENDING;
    }

    // The author can accept several offers to talk them through, until one of the exchanges is completed
    public function accept(User $user, PostOffer $offer): bool
    {
        return $user->id === $offer->post->user_id
            && $offer->status === PostOfferStatus::PENDING
            && in_array($offer->post->status, [PostStatus::AVAILABLE, PostStatus::IN_PROGRESS], true);
    }
}
