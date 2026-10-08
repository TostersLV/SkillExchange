<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\PostOfferStatus;
use App\PostStatus;

class PostPolicy
{
    // Anyone can view the post when it's available and the author can too regardless of the status
    public function view(User $user, Post $post): bool
    {
        return $post->status === PostStatus::AVAILABLE || $user->id === $post->user_id;
    }

    // Only author can update their post, and only while nobody has sent an offer, so nobody's terms change under them
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id
            && $post->status === PostStatus::AVAILABLE
            && $post->offers()->where('status', PostOfferStatus::PENDING)->doesntExist();
    }

    // Only author can delete their post, and only if nobody ever sent an offer on it, so no shared history is lost
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id
            && $post->status === PostStatus::AVAILABLE
            && $post->offers()->doesntExist();
    }

    // A post that has offers can't be deleted, but its author can close it so it stops taking offers
    public function close(User $user, Post $post): bool
    {
        return $user->id === $post->user_id
            && $post->status === PostStatus::AVAILABLE
            && $post->offers()->exists();
    }
}
