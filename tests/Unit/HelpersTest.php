<?php

use CodeLieutenant\LaravelPgEnum\Helpers;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Facades\DB;
use Workbench\App\Enums\CustomNonBackedEnum;

enum BackedStringEnum: string
{
    case VAL = 'value';
    case OTHER = 'other';
}

enum BackedIntEnum: int
{
    case VAL = 1;
    case OTHER = 2;
    case CamelCaseToSnakeCase = 3;
}

enum NonBackedEnum
{
    case VAL;
    case OTHER;
    case CamelCaseToSnakeCase;
}

it('formats name for database usage', function () {
    $name = Helpers::formatNameForDatabase(new PostgresGrammar(), 'name');
    expect($name)->toBe('\'name\'');
});

it('formats enum values', function (array $cases, string $expected) {
    $value = Helpers::formatPgEnumValuesForDatabase(DB::getPdo(), $cases);

    expect($value)
        ->toBeString()
        ->toBe($expected);
})->with([
    ['cases' => BackedIntEnum::cases(), 'expected' => "'val', 'other', 'camel_case_to_snake_case'"],
    ['cases' => BackedStringEnum::cases(), 'expected' => "'value', 'other'"],
    ['cases' => NonBackedEnum::cases(), 'expected' => "'val', 'other', 'camel_case_to_snake_case'"],
    ['cases' => ['val', 'other'], 'expected' => "'val', 'other'"],
]);

it('extracts name and values from PHP enum', function (string $enum, string $expectedName, string $expectedValues) {
    $values = Helpers::extractNameAndValueFromEnum(DB::getPdo(), $enum);

    expect($values)
        ->toBeArray()
        ->toHaveKeys(['name', 'values'])
        ->and($values['name'])
        ->toBeString()
        ->toBe($expectedName)
        ->and($values['values'])
        ->toBeString()
        ->toBe($expectedValues);
})->with([
    [
        'enum' => BackedStringEnum::class,
        'expectedName' => 'backed_string_enum',
        'expectedValues' => "'value', 'other'",
    ],
    [
        'enum' => BackedIntEnum::class,
        'expectedName' => 'backed_int_enum',
        'expectedValues' => "'val', 'other', 'camel_case_to_snake_case'",
    ],
    [
        'enum' => NonBackedEnum::class,
        'expectedName' => 'non_backed_enum',
        'expectedValues' => "'val', 'other', 'camel_case_to_snake_case'",
    ],
    [
        'enum' => CustomNonBackedEnum::class,
        'expectedName' => 'custom_non_backed_enum',
        'expectedValues' => "'val', 'other', 'camel_case_to_snake_case'",
    ],
]);
