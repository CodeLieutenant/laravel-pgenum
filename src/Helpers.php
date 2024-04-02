<?php

declare(strict_types=1);

namespace Codelieutenant\LaravelPgenum;

use Codelieutenant\LaravelPgenum\Exceptions\InvalidEnumType;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;
use ReflectionEnum;
use StringBackedEnum;
use UnitEnum;

final class Helpers
{
    public static function formatNameForDatabase(Connection $conn, string $name): string
    {
        if (class_exists($name)) {
            $reflection = new ReflectionEnum($name);
            $name = Str::snake($reflection->getName());
        }

        return $conn->getSchemaGrammar()->wrap($name);
    }

    public static function formatValuesForDatabase(Connection $conn, array $values): string
    {
        $pdo = $conn->getPdo();
        $value = array_reduce(
            $values,
            static fn($carry, $status) => $carry . ', ' . $pdo->quote(
                    match (true) {
                        is_string($status) => $status,
                        $status instanceof StringBackedEnum => $status->value,
                        $status instanceof UnitEnum => Str::snake($status->name),
                        default => throw new InvalidEnumType(),
                    },
                ),
            ''
        );

        return ltrim($value, ' ,');
    }
}