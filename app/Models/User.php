<?php

namespace App\Models;

use App\PostOfferStatus;
use App\PostStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property-read float|null $reputation
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
        ];
    }

    /**
     * The name shown for an account that has been deactivated, whose stored username is cleared.
     */
    public const DeletedUsername = 'Deleted user';

    /**
     * Deactivated accounts store no username, so they are shown under one shared placeholder name.
     *
     * @return Attribute<string, never>
     */
    protected function username(): Attribute
    {
        return Attribute::make(get: fn (?string $value): string => $value ?? self::DeletedUsername);
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
     * The public URL of the user's uploaded profile picture, or null when they have none.
     */
    public function profilePictureUrl(): ?string
    {
        return $this->profile_picture
            ? Storage::disk('public')->url($this->profile_picture)
            : null;
    }

    /**
     * Delete the user's stored profile picture file and clear the column.
     */
    public function removeProfilePicture(): void
    {
        if ($this->profile_picture) {
            Storage::disk('public')->delete($this->profile_picture);
        }

        $this->forceFill(['profile_picture' => null])->save();
    }

    /**
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * The user's rating: the average of all reviews other members left about them, calculated from the
     * reviews themselves so it can never drift out of sync. Lists load it in one query with
     * withAvg('reviewsReceived', 'review'); otherwise it's calculated on first use.
     *
     * @return Attribute<float|null, never>
     */
    protected function reputation(): Attribute
    {
        return Attribute::make(get: function (): ?float {
            $average = array_key_exists('reviews_received_avg_review', $this->attributes)
                ? $this->attributes['reviews_received_avg_review']
                : $this->reviewsReceived()->avg('review');

            return $average === null ? null : round((float) $average, 2);
        })->shouldCache();
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

    /**
     * Whether the user is in the middle of an exchange that someone else depends on.
     */
    public function hasActiveExchanges(): bool
    {
        return $this->exchanges()->whereRelation('post', 'status', PostStatus::IN_PROGRESS)->exists();
    }

    /**
     * Close the account without erasing anyone else's history: open offers and posts are withdrawn, personal
     * data is cleared, and the row is kept so past exchanges, chats and reviews stay intact for the other members.
     */
    public function deactivate(): void
    {
        if ($this->profile_picture) {
            Storage::disk('public')->delete($this->profile_picture);
        }

        DB::transaction(function () {
            PostOffer::query()
                ->where('status', PostOfferStatus::PENDING)
                ->where('user_id', $this->id)
                ->update(['status' => PostOfferStatus::WITHDRAWN]);

            PostOffer::query()
                ->where('status', PostOfferStatus::PENDING)
                ->whereRelation('post', 'user_id', $this->id)
                ->update(['status' => PostOfferStatus::REJECTED]);

            $this->posts()->where('status', PostStatus::AVAILABLE)->doesntHave('offers')->delete();
            $this->posts()->where('status', PostStatus::AVAILABLE)->update(['status' => PostStatus::CANCELLED]);

            $this->forceFill([
                'username' => null,
                'bio' => null,
                'email' => "deleted-{$this->id}@skillexchange.invalid",
                'email_verified_at' => null,
                'password' => Str::random(64),
                'profile_picture' => null,
                'remember_token' => null,
            ])->save();

            DB::table('sessions')->where('user_id', $this->id)->delete();
        });
    }

    /**
     * Among the given posts, the ones where this user's latest offer was declined or its exchange cancelled,
     * with how it ended. Only the posts being shown are looked up, so it doesn't grow with the user's history.
     *
     * @param  Collection<int, int>|array<int, int>  $postIds
     * @return Collection<int, PostOfferStatus> keyed by post id
     */
    public function closedOfferStatusesByPost(Collection|array $postIds): Collection
    {
        return PostOffer::query()
            ->where('user_id', $this->id)
            ->whereIn('post_id', $postIds)
            ->oldest('updated_at')
            ->oldest('id')
            ->get(['post_id', 'status'])
            ->mapWithKeys(fn (PostOffer $offer) => [$offer->post_id => $offer->status])
            ->filter(fn (PostOfferStatus $status) => $status->isClosed());
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
