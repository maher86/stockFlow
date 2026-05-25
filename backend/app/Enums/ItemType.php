<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemType: string
{
    case Pants = 'pants';
    case Shorts = 'shorts';
    case Shirt = 'shirt';
    case Skirt = 'skirt';
    case Jacket = 'jacket';
    case Dress = 'dress';
    case Other = 'other';
}
