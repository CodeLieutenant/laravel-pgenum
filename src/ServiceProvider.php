<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum;

use CodeLieutenant\LaravelPgEnum\Mixins\BlueprintExtensions;
use CodeLieutenant\LaravelPgEnum\Mixins\GrammarExtensions;
use CodeLieutenant\LaravelPgEnum\Mixins\SchemaExtensions;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function register(): void
    {
        Schema::mixin(new SchemaExtensions());
        Grammar::mixin(new GrammarExtensions());
        Blueprint::mixin(new BlueprintExtensions());
    }
}
