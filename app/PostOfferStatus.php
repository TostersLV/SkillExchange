<?php

namespace App;

enum PostOfferStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case WITHDRAWN = 'withdrawn';

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'amber',
            self::ACCEPTED => 'green',
            self::REJECTED, self::CANCELLED, self::WITHDRAWN => 'zinc',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::REJECTED => 'Declined',
            default => ucfirst($this->value),
        };
    }

    /**
     * Whether the offer ended without the sender's choice, so they should be told about it.
     */
    public function isClosed(): bool
    {
        return in_array($this, self::closed(), true);
    }

    /**
     * Statuses of offers that ended without the sender's choice: declined by the author, or a called-off exchange.
     *
     * @return list<self>
     */
    public static function closed(): array
    {
        return [self::REJECTED, self::CANCELLED];
    }
}
