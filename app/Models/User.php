<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\PostOfferStatus;
use App\PostStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @property string|null $reputation
 */
#[Fillable(['username', 'bio', 'email', 'password', 'profile_picture'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'reputation' => 'decimal:2',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->username, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * The reviews other members left about this user.
     *
     * @return HasMany<Review, $this>
     */
    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * Accepted offers the user takes part in, either as the post's author or as the one who offered.
     *
     * @return Builder<PostOffer>
     */
    public function exchanges(): Builder
    {
        return PostOffer::query()
            ->where('status', PostOfferStatus::ACCEPTED)
            ->where(fn (Builder $query) => $query->where('user_id', $this->id)->orWhereRelation('post', 'user_id', $this->id));
    }

    public function completedExchangesCount(): int
    {
        return $this->exchanges()->whereRelation('post', 'status', PostStatus::COMPLETED)->count();
    }

    /**
     * Count the offers other members sent on this user's posts that still wait for an answer.
     */
    public function pendingReceivedOffersCount(): int
    {
        return PostOffer::query()
            ->whereRelation('post', 'user_id', $this->id)
            ->where('status', PostOfferStatus::PENDING)
            ->count();
    }
}
