<?php

declare(strict_types=1);

namespace Workbench\App\Enums;

enum CustomBackedIntEnum: int
{
    case VAL = 1;
    case OTHER = 2;
    case CamelCaseToSnakeCase = 3;
}
