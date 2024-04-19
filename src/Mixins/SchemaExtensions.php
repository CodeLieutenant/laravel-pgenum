<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum\Mixins;

use Closure;
use CodeLieutenant\LaravelPgEnum\Enums\Direction;
use CodeLieutenant\LaravelPgEnum\Helpers;
use Illuminate\Database\Schema\Builder;

class SchemaExtensions
{
    public function createEnum(): Closure
    {
        return function (string $name, ?array $values = null): void {
            /** @var $this Builder */
            $conn = $this->getConnection();

            $value = match ($values === null) {
                true => Helpers::extractNameAndValueFromEnum($conn->getPdo(), $name),
                false => [
                    'name' => Helpers::formatNameForDatabase($conn->getSchemaGrammar(), $name),
                    'values' => Helpers::formatPgEnumValuesForDatabase($conn->getPdo(), $values),
                ],
            };

            $conn->statement("CREATE TYPE {$value['name']} AS ENUM ({$value['values']});");
        };
    }

    public function createEnumIfNotExists(): Closure
    {
        return function (string $name, ?array $values = null): void {
            /** @var $this Builder */
            $conn = $this->getConnection();

            $value = match ($values === null) {
                true => Helpers::extractNameAndValueFromEnum($conn->getPdo(), $name),
                false => [
                    'name' => Helpers::formatNameForDatabase($conn->getSchemaGrammar(), $name),
                    'values' => Helpers::formatPgEnumValuesForDatabase($conn->getPdo(), $values),
                ],
            };

            $conn->statement(
                <<<SQL
                DO $$ BEGIN
                    CREATE TYPE {$value['name']} AS ENUM({$value['values']});
                EXCEPTION
                    WHEN duplicate_object THEN null;
                END $$;
                SQL
            );
        };
    }

    public function dropEnum(): Closure
    {
        return function (string $name): void {
            /** @var $this Builder */
            $conn = $this->getConnection();
            $name = Helpers::formatNameForDatabase($conn->getSchemaGrammar(), $name);
            $conn->statement("DROP TYPE $name;");
        };
    }

    public function dropEnumIfExists(): Closure
    {
        return function (string $name): void {
            /** @var $this Builder */
            $conn = $this->getConnection();
            $name = Helpers::formatNameForDatabase($conn->getSchemaGrammar(), $name);
            $conn->statement(
                <<<SQL
                DO $$
                BEGIN
                    DROP TYPE $name;
                EXCEPTION
                    WHEN undefined_object THEN null;
                END $$;
                SQL
            );
        };
    }

    public function renameEnum(): Closure
    {
        return function (string $type, string $newName): void {
            /** @var $this Builder */
            $conn = $this->getConnection();
            $grammar = $conn->getSchemaGrammar();
            $type = Helpers::formatNameForDatabase($grammar, $type);
            $newName = Helpers::formatNameForDatabase($grammar, $newName);
            $conn->statement("ALTER TYPE $type RENAME TO $newName;");
        };
    }

    public function renameEnumValue(): Closure
    {
        return function (string $type, string $oldName, string $newName): void {
            /** @var $this Builder */
            $conn = $this->getConnection();
            $grammar = $conn->getSchemaGrammar();
            $oldName = Helpers::formatNameForDatabase($grammar, $oldName);
            $newName = Helpers::formatNameForDatabase($grammar, $newName);
            $conn->statement("ALTER TYPE $type RENAME VALUE $oldName TO $newName;");
        };
    }

    public function addEnumValue(): Closure
    {
        return function (
            string $type,
            string $value,
            ?Direction $direction = null,
            ?string $otherValue = null,
            bool $ifNotExists = true
        ) {
            /** @var $this Builder */
            $conn = $this->getConnection();
            $type = Helpers::formatNameForDatabase($conn->getSchemaGrammar(), $type);
            $stmt = "ALTER TYPE $type ADD VALUE";

            if ($ifNotExists) {
                $stmt .= ' IF NOT EXISTS';
            }

            $stmt .= ' '.Helpers::formatPgEnumValuesForDatabase($conn->getPdo(), [$value]);

            if ($direction && $otherValue) {
                $stmt .= " $direction->value $otherValue";
            }

            $conn->statement($stmt);
        };
    }
}
