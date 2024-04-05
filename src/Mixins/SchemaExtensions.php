<?php

declare(strict_types=1);

namespace CodeLieutenant\LaravelPgEnum\Mixins;

use Closure;
use CodeLieutenant\LaravelPgEnum\Enums\Direction;
use CodeLieutenant\LaravelPgEnum\Helpers;
use Illuminate\Database\Schema\Builder;
use PDO;

class SchemaExtensions
{
    public function createEnum(): Closure
    {
        return function (string $name, array $values): void {
            /** @var $this Builder */
            $conn = $this->getConnection();
            $name = Helpers::formatNameForDatabase($conn->getSchemaGrammar(), $name);
            $value = Helpers::formatValuesForDatabase(self::getPdo($conn), $values);
            $conn->statement("CREATE TYPE $name AS ENUM ($value);");
        };
    }

    private function getPdo(Builder $conn): PDO
    {
        $pdo = $conn->getPdo();

        return match (true) {
            $pdo instanceof Closure => $pdo(),
            $pdo instanceof PDO => $pdo,
        };
    }

    public function createEnumIfNotExists(): Closure
    {
        return function (string $name, array $values): void {
            /** @var $this Builder */
            $conn = $this->getConnection();
            $name = Helpers::formatNameForDatabase($conn->getSchemaGrammar(), $name);
            $value = Helpers::formatValuesForDatabase(self::getPdo($conn), $values);

            $conn->statement(
                <<<SQL
                DO $$ BEGIN
                    CREATE TYPE $name AS ENUM($value);
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
            $name = Helpers::formatNameForDatabase($conn, $name);
            $conn->statement("DROP TYPE $name;");
        };
    }

    public function dropEnumIfExists(): Closure
    {
        return function (string $name): void {
            /** @var $this Builder */
            $conn = $this->getConnection();
            $name = Helpers::formatNameForDatabase($conn, $name);
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
            $type = Helpers::formatNameForDatabase($conn, $type);
            $newName = Helpers::formatNameForDatabase($conn, $newName);
            $conn->statement("ALTER TYPE $type RENAME TO $newName;");
        };
    }

    public function renameEnumValue(): Closure
    {
        return function (string $type, string $oldName, string $newName): void {
            /** @var $this Builder */
            $conn = $this->getConnection();
            $oldName = Helpers::formatNameForDatabase($conn, $oldName);
            $newName = Helpers::formatNameForDatabase($conn, $newName);
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
            bool $ifNotExists = false
        ) {
            /** @var $this Builder */
            $conn = $this->getConnection();
            $type = Helpers::formatNameForDatabase($conn, $type);
            $stmt = "ALTER TYPE $type ADD VALUE";

            if ($ifNotExists) {
                $stmt .= ' IF NOT EXISTS';
            }

            $stmt .= ' '.Helpers::formatValuesForDatabase($conn, [$value]);

            if ($direction && $otherValue) {
                $stmt .= " $direction->value $otherValue";
            }

            $conn->statement($stmt);
        };
    }
}
