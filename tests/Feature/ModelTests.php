<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\App\Enums\CustomBackedIntEnum;
use Workbench\App\Enums\CustomBackedStringEnum;
use Workbench\App\Enums\CustomNonBackedEnum;
use Workbench\App\Models\Custom;

use function Pest\Laravel\assertDatabaseCount;

uses(RefreshDatabase::class);

test('test insert', function () {
    $value = Custom::create([
        'another_string_backed_enum' => CustomBackedStringEnum::OTHER,
        'string_backed_enum' => CustomBackedStringEnum::VAL,
        'int_backed_enum' => CustomBackedIntEnum::VAL,
        'non_backed_enum' => CustomNonBackedEnum::VAL,
    ]);

    expect($value->string_backed_enum)
        ->toBe(CustomBackedStringEnum::VAL)
        ->and($value->int_backed_enum)
        ->toBe(CustomBackedIntEnum::VAL)
        ->and($value->non_backed_enum)
        ->toBe(CustomNonBackedEnum::VAL);

    assertDatabaseCount('customs', 1);
});
