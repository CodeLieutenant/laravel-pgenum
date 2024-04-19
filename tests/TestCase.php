<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

#[WithMigration]
class TestCase extends OrchestraTestCase
{
    use DatabaseTransactions;
    use WithWorkbench;
}
