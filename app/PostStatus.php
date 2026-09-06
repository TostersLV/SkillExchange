<?php

namespace App;

enum PostStatus: string
{
    case AVAILABLE = 'available';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
