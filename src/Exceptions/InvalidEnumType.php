<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum\Exceptions;

use RuntimeException;
use Throwable;

class InvalidEnumType extends RuntimeException
{
    public function __construct(
        string $message = 'Invalid type for PgEnums, use strings or Enum classes',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
