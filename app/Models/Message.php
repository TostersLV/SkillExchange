<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $post_offer_id
 * @property int $user_id
 * @property string $body
 */
#[Fillable(['post_offer_id', 'user_id', 'body'])]
class Message extends Model
{
    /**
     * The offer/exchange this message was sent in.
     *
     * @return BelongsTo<PostOffer, $this>
     */
    public function postOffer(): BelongsTo
    {
        return $this->belongsTo(PostOffer::class);
    }

    /**
     * The user who sent this message.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
