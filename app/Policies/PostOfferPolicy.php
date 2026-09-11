<?php

namespace App\Policies;

use App\Models\PostOffer;
use App\Models\User;
use App\PostOfferStatus;

class PostOfferPolicy
{
    public function cancel(User $user, PostOffer $offer): bool
    {
        return $user->id === $offer->user_id && $offer->status === PostOfferStatus::PENDING;
    }
    public function reject(User $user, PostOffer $offer): bool
    {
        return $user->id === $offer->post->user_id && $offer->status === PostOfferStatus::PENDING;
    }
    public function accept(User $user, PostOffer $offer): bool
    {
        return $user->id === $offer->post->user_id && $offer->status === PostOfferStatus::PENDING;
    }

}
