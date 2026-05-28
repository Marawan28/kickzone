<?php

declare(strict_types=1);

namespace App\Enums;


enum TransactionStatus: string
{
    case Pending   = 'pending';
    case Accepted  = 'accepted';
    case Rejected  = 'rejected';
    case Cancelled = 'cancelled';
}
