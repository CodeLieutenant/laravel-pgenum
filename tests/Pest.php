<?php

declare(strict_types=1);

use CodeLieutenant\LaravelPgEnum\Tests\TestCase;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;

uses(TestCase::class)->in(__DIR__);

function assertEnumExists(string $name)
{
    $values = DB::select('SELECT * FROM pg_type where typname = ?', [$name]);
    assertNotEmpty($values);
}

function assertEnumNotExists(string $name)
{
    $values = DB::select('SELECT * FROM pg_type where typname = ?', [$name]);
    assertEmpty($values);
}

function assertEnumHasValues(string $name, array $values)
{
    $vals = Str::of(DB::select("SELECT enum_range(null::$name)")[0]->enum_range)
        ->ltrim('{')
        ->rtrim('}')
        ->explode(',')
        ->toArray();

    assertEquals($values, $vals);
}
