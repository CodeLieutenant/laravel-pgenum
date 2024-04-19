<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use CodeLieutenant\LaravelPgEnum\Casts\EnumCast;
use Illuminate\Database\Eloquent\Model;
use Workbench\App\Enums\CustomBackedIntEnum;
use Workbench\App\Enums\CustomBackedStringEnum;
use Workbench\App\Enums\CustomNonBackedEnum;

class Custom extends Model
{
    protected $fillable = [
        'string_backed_enum',
        'another_string_backed_enum',
        'int_backed_enum',
        'non_backed_enum',
    ];

    protected $casts = [
        'string_backed_enum' => CustomBackedStringEnum::class,
        'another_string_backed_enum' => EnumCast::class.':'.CustomBackedStringEnum::class,
        'int_backed_enum' => EnumCast::class.':'.CustomBackedIntEnum::class,
        'non_backed_enum' => EnumCast::class.':'.CustomNonBackedEnum::class,
    ];

    public function casts(): array
    {
        return [
            'another_string_backed_enum' => EnumCast::class.':'.CustomBackedStringEnum::class,
            'string_backed_enum' => CustomBackedStringEnum::class,
            'int_backed_enum' => EnumCast::class.':'.CustomBackedIntEnum::class,
            'non_backed_enum' => EnumCast::class.':'.CustomNonBackedEnum::class,
        ];
    }
}
