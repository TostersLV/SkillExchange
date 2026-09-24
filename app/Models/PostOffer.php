<?php

namespace App\Models;

use App\PostOfferStatus;
use App\PostStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string|null $message
 * @property PostOfferStatus $status
 */
#[Fillable(['post_id', 'user_id', 'message', 'status'])]
class PostOffer extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostOfferStatus::class,
        ];
    }

    public function hasBeenCompletedBy(User $user): bool
    {
        return $this->completeOffers()->where('user_id', $user->id)->exists();
    }

    public function hasBeenCompletedByOther(User $user): bool
    {
        return $this->completeOffers()->where('user_id', '!=', $user->id)->exists();
    }

    public function hasBeenReviewedBy(User $user): bool
    {
        return $this->reviews()->where('reviewer_id', $user->id)->exists();
    }

    /**
     * @param  Builder<PostOffer>  $query
     * @return Builder<PostOffer>
     */
    public function scopeAwaitingReviewBy(Builder $query, User $user): Builder
    {
        return $query->where('status', PostOfferStatus::ACCEPTED)->whereRelation('post', 'status', PostStatus::COMPLETED)->where(fn ($query) => $query->where('user_id', $user->id)->orWhereRelation('post', 'user_id', $user->id))->whereDoesntHave('reviews', fn ($query) => $query->where('reviewer_id', $user->id));
    }

    /**
     * The post the offer was made on.
     *
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * The user who sent the offer.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The reviews left about this offer.
     *
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * The confirmations that this offer's exchange is complete, one per participant.
     *
     * @return HasMany<CompleteOffer, $this>
     */
    public function completeOffers(): HasMany
    {
        return $this->hasMany(CompleteOffer::class);
    }

    /**
     * The chat messages exchanged between the two participants.
     *
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->oldest();
    }
}
