<?php

namespace App;

enum PostStatus: string
{
    case AVAILABLE = 'available';
    case IN_PROGRESS = 'In progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
