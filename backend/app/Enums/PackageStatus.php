<?php

declare(strict_types=1);

namespace App\Enums;

enum PackageStatus: string
{
    case Unsorted = 'unsorted';
    case Sorted = 'sorted';
    case Processing = 'processing';
    case Complete = 'complete';
}
