<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $offering_skill
 * @property string $looking_skill
 * @property string|null $description
 * @property string $status
 */
#[Fillable(['user_id', 'category_id', 'offering_skill', 'looking_skill', 'description', 'status'])]
class Post extends Model
{
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<PostOffer, $this>
     */
    public function offers(): HasMany
    {
        return $this->hasMany(PostOffer::class);
    }
}
