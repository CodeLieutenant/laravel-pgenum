<?php

use CodeLieutenant\LaravelPgEnum\Helpers;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Facades\DB;

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
    expect($name)->toBe('"name"');
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
