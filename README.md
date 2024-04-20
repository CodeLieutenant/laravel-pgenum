# Laravel Postgres Enums

> Laravel's answer to Postgres Real Enums

[![Tests](https://github.com/CodeLieutenant/laravel-pgenum/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/CodeLieutenant/laravel-pgenum/actions/workflows/test.yml/badge.svg?branch=master)
[![GitHub issues](https://img.shields.io/github/issues/CodeLieutenant/laravel-pgenum?label=Github%20Issues)](https://img.shields.io/github/issues/CodeLieutenant/laravel-pgenum?label=Github%20Issues)
[![GitHub stars](https://img.shields.io/github/stars/CodeLieutenant/laravel-pgenum?label=Github%20Stars)](https://img.shields.io/github/stars/CodeLieutenant/laravel-pgenum?label=Github%20Stars)
[![GitHub license](https://img.shields.io/github/license/CodeLieutenant/laravel-pgenum?label=Licence)](https://img.shields.io/github/license/CodeLieutenant/laravel-pgenum?label=Licence)
![GitHub Sponsors](https://img.shields.io/github/sponsors/CodeLieutenant)
![GitHub commit activity](https://img.shields.io/github/commit-activity/m/CodeLieutenant/laravel-pgenum)

## Inspiration

n Laravel’s migration, Enums already exist, yeah? Yes, but not quite. These Enums are not actual Database Enums.
These enums are just a string type with CHECK constraints.
So I said to myself, it would be really nice to use `real` Postgres enums in Laravel.

## Getting started

### Installing

```shell
composer require codelieutenant/laravel-pgenum
```

### Usage in Migrations

##### Schema API

This library extends Laravel's `Illuminate\Support\Facades\Schema` using `Illuminate\Support\Traits\Macroable`

```php
Schema::createEnum(string $name, ?array $values = null);
Schema::createEnumIfNotExists((string $name, ?array $values = null);

Schema::addEnumValue(string $type, string $value, ?Direction $direction = null, ?string $otherValue = null, bool $ifNotExists = true);
Schema::renameEnumValue(string $type, string $oldName, string $newName);

Schema::dropEnum(string $name);
Schema::dropEnumIfExists(string $name);
```

Schema API also works with PHP Enums by default

```php

enum MyEnum: string
{
    case MyValue = 'value';
}

// Migrations
// Generated SQL
// CREATE TYPE my_enum ENUM ('value');
Schema::createEnum(MyEnum::class);

// Generated SQL
// CREATE TYPE my_enum ENUM ('value');
Schema::dropEnum(MyEnum::class);
```

##### Blueprint API

There is only one function for Blueprint `enumeration` to create a column in table.

```php
// Accepts PHP Enums and Strings
\Illuminate\Database\Schema\Blueprint::enumeration(string $name);
```

### Full Migration Example

```php

public function up(): void
{
    // Postgres Enums must be known before Table is created
    // This is my, `createEnum` must be seperated and before `Schema::create`
    Schema::createEnum(MyEnum::class);
    Schema::create('users', function(Blueprint $table) {
        $table->id();
        $table->enumeration(MyEnum::class);   
    });
}

public function down(): void
{
    Schema::dropEnum(MyEnum::class);
}
```

### Using PHP Enums

Laravel already supports PHP Enums by default, but
the nature of this library needs a bit more than what's
provided by Laravel by default.
PHP Enums (in all forms -> string/int backed, non-backed)
are supported, here is the example:

```php

use \Illuminate\Database\Eloquent\Model;
use \CodeLieutenant\LaravelPgEnum\Casts\EnumCast;

enum Role : string
{
    case Admin = 'admin';
    case User = 'user'; 
}

class User extends Model
{
    // For Laravel < 11.0 use $casts property
    public function casts(): array
    {
        return [
            // This is supported by default
            // as long as PHP Enum is backed by string
            'role' => Role::class,
            
            // For non-backed/int backed PHP Enums
            // custom converter is needed, this also
            // can be applied to string backed enums (but not needed)
            // This is due to the limitation of Postgres Enums
            // as it requires values to be string named, and not INTs
            // Using Role::class as example, can be any other PHP Enum
            'non_string_backed_enum' => EnumCast::class ':' . Role::class
        ];    
    }
}
```
