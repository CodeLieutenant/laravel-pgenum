<?php

declare(strict_types=1);

namespace Workbench\App\Enums;

enum CustomNonBackedEnum
{
    case VAL;
    case OTHER;
    case CamelCaseToSnakeCase;
}
