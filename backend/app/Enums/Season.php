<?php

declare(strict_types=1);

namespace App\Enums;

enum Season: string
{
    case Summer = 'summer';
    case Winter = 'winter';
    case Spring = 'spring';
    case AllSeasons = 'all_seasons';
}
