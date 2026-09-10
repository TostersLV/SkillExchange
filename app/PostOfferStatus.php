<?php

namespace App;

enum PostOfferStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'amber',
            self::ACCEPTED => 'green',
            self::REJECTED => 'zinc',
        };
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
