<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemConditionValue: string
{
    case Perfect = 'perfect';
    case Good = 'good';
    case Normal = 'normal';
    case Zero = 'zero';
}
