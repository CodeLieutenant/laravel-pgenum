<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum;

use BackedEnum;
use CodeLieutenant\LaravelPgEnum\Exceptions\InvalidEnumType;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Str;
use PDO;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use ReflectionEnumUnitCase;
use ReflectionException;
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
            $name = Str::snake($reflection->getShortName());
        }

        return $grammar->wrap($name);
    }

    public static function extractNameAndValueFromEnum(PDO $pdo, string $name): array
    {
        $reflection = new ReflectionEnum($name);

        return [
            'name' => Str::snake($reflection->getShortName()),
            'values' => self::formatPgEnumValuesForDatabase($pdo, $reflection->getCases()),
        ];
    }

    public static function formatEnumCaseName(string $name): string
    {
        return ctype_upper($name) ? Str::lower($name) : Str::snake($name);
    }

    public static function formatPgEnumValuesForDatabase(PDO $pdo, array $values): string
    {
        $value = array_reduce(
            $values,
            static function ($carry, $status) use ($pdo) {
                return $carry.', '.$pdo->quote(
                    match (true) {
                        is_string($status) => $status,
                        $status instanceof ReflectionEnumBackedCase => is_string(
                            $status->getBackingValue()
                        ) ? $status->getBackingValue() : Helpers::formatEnumCaseName($status->getName()),
                        $status instanceof ReflectionEnumUnitCase => Helpers::formatEnumCaseName($status->getName()),
                        $status instanceof BackedEnum && is_string($status->value) => $status->value,
                        $status instanceof UnitEnum => Helpers::formatEnumCaseName($status->name),
                        default => throw new InvalidEnumType(),
                    },
                );
            },
            ''
        );

        return ltrim($value, ' ,');
    }
}
