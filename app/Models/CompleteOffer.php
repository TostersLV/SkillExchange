<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $post_offer_id
 * @property int $user_id
 */
#[Fillable(['post_offer_id', 'user_id'])]
class CompleteOffer extends Model
{
    /**
     * The offer that was confirmed as complete.
     *
     * @return BelongsTo<PostOffer, $this>
     */
    public function postOffer(): BelongsTo
    {
        return $this->belongsTo(PostOffer::class);
    }

    /**
     * The user who confirmed the completion.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
