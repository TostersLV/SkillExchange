<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $post_offer_id
 * @property int|null $reviewer_id
 * @property int|null $reviewee_id
 * @property int $review
 */
#[Fillable(['post_offer_id', 'reviewer_id', 'reviewee_id', 'review'])]
class Review extends Model
{
    /**
     * The offer this review was left about.
     *
     * @return BelongsTo<PostOffer, $this>
     */
    public function postOffer(): BelongsTo
    {
        return $this->belongsTo(PostOffer::class);
    }

    /**
     * The user who left the review.
     *
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * The user being reviewed.
     *
     * @return BelongsTo<User, $this>
     */
    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}
