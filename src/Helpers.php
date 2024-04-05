<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum;

use BackedEnum;
use CodeLieutenant\LaravelPgEnum\Exceptions\InvalidEnumType;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Str;
use PDO;
use ReflectionEnum;
use ReflectionException;
use StringBackedEnum;
use UnitEnum;

final class Helpers
{
    /**
     * @throws ReflectionException
     */
    public static function formatNameForDatabase(Grammar $grammar, string $name): string
    {
        if (class_exists($name)) {
            $reflection = new ReflectionEnum($name);
            $name = Str::snake($reflection->getName());
        }

        return $grammar->wrap($name);
    }

    public static function formatPgEnumValuesForDatabase(PDO $pdo, array $values): string
    {
        $value = array_reduce(
            $values,
            static function ($carry, $status) use ($pdo) {
                return $carry.', '.$pdo->quote(
                    match (true) {
                        is_string($status) => $status,
                        $status instanceof BackedEnum && is_string($status->value) => $status->value,
                        $status instanceof UnitEnum => ctype_upper($status->name) ? Str::lower(
                            $status->name
                        ) : Str::snake($status->name),
                        default => throw new InvalidEnumType(),
                    },
                );
            },
            ''
        );

        return ltrim($value, ' ,');
    }
}
