<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum\Casts;

use CodeLieutenant\LaravelPgEnum\Helpers;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use ReflectionEnum;

class EnumCast implements CastsAttributes
{
    private readonly ReflectionEnum $reflect;

    private readonly bool $isStringBacked;

    public function __construct(private readonly string $enum)
    {
        $this->reflect = new ReflectionEnum($enum);
        $this->isStringBacked = $this->reflect->isBacked() && $this->reflect->getBackingType()?->getName() === 'string';
    }

    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        if ($this->isStringBacked) {
            return $this->enum::from($value);
        }

        return $this->reflect->getCase($value)->getValue();
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (! ($value instanceof $this->enum)) {
            throw new InvalidArgumentException('Value must be an instance of '.$this->enum);
        }

        if ($this->isStringBacked) {
            return $value->value;
        }

        return Helpers::formatEnumCaseName($value->name);
    }
}
