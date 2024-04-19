<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum\Tests\Feature;

use function Pest\Laravel\artisan;

it('runs migrations successfully', function () {
    artisan('migrate')
        ->assertExitCode(0);

    artisan('migrate:rollback')
        ->assertExitCode(0);
});
