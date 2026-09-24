<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\PostStatus;

class PostPolicy
{
    // Anyone can view the post when it's available and the author can too regardless of the status
    public function view(User $user, Post $post): bool
    {
        return $post->status === PostStatus::AVAILABLE || $user->id === $post->user_id;
    }

    // Only author can update their post
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // Only author can delete their post
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
