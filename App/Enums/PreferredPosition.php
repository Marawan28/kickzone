<?php

declare(strict_types=1);

namespace App\Enums;

enum PreferredPosition: string
{
    case Attacker   = 'attacker'; 
    case Midfielder = 'midfielder';
    case Defender   = 'defender';
    case Goalkeeper = 'goalkeeper';
};
