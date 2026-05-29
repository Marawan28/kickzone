<?php

declare(strict_types=1);

namespace App\Enums;

enum MatchmakingStatus: string
{
    case Waiting   = 'waiting';
    case Matched   = 'matched';
    case Cancelled = 'cancelled';
    case Expired   = 'expired';
}
