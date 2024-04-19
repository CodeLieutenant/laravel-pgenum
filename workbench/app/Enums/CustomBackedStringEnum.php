<?php

declare(strict_types=1);

namespace Workbench\App\Enums;

enum CustomBackedStringEnum: string
{
    case VAL = 'value';
    case OTHER = 'other';
}
