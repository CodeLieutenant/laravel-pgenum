<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum\Mixins;

use Closure;
use Illuminate\Database\Schema\Blueprint;

class BlueprintExtensions
{
    public function enumeration(): Closure
    {
        return function (string $name, string $type, array $options = []) {
            /** @var $this Blueprint */
            return $this->addColumn('enumeration', $name, [
                'pg_enum' => $type,
                ...$options,
            ]);
        };
    }
}