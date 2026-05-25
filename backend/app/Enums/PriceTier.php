<?php

declare(strict_types=1);

namespace App\Enums;

enum PriceTier: string
{
    case N3 = 'N3';
    case N2 = 'N2';
    case N1 = 'N1';
    case Zero = 'zero';
}
