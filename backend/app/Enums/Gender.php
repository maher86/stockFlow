<?php

declare(strict_types=1);

namespace App\Enums;

enum Gender: string
{
    case Men = 'men';
    case Women = 'women';
    case Boys = 'boys';
    case Girls = 'girls';
}
