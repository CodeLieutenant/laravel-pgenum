<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum\Mixins;

use Closure;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Str;
use ReflectionEnum;

class GrammarExtensions
{
    public function typeEnumeration(): Closure
    {
        return function (ColumnDefinition $columnDefinition) {
            return match (class_exists($name = $columnDefinition['pg_enum'])) {
                true => Str::snake((new ReflectionEnum($name))->getName()),
                false => $name
            };
        };
    }
}
