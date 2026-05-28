<?php

declare(strict_types=1);

namespace App\Enums;


enum MatchStatus: string
{
    case Open     = 'open';
    case Full     = 'full';
    case Finished = 'finished';
}
