<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum\Enums;

enum Direction: string
{
    case BEFORE = 'BEFORE';
    case AFTER = 'AFTER';
}
