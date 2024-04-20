<?php

declare(strict_types=1);

use CodeLieutenant\LaravelPgEnum\Enums\Direction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

afterEach(function () {
    try {
        DB::statement('DROP TYPE test_enum;');
    } catch (Exception$e) {
    }
});

test('create new enum with values', function () {
    Schema::createEnum('test_enum', ['one', 'two', 'three']);
    assertEnumExists('test_enum');
    assertEnumHasValues('test_enum', ['one', 'two', 'three']);
    Schema::dropEnum('test_enum');
    assertEnumNotExists('test_enum');
});

test('create same enum twice', function () {
    Schema::createEnum('test_enum', ['one', 'two', 'three']);
    Schema::createEnum('test_enum', ['one', 'two', 'three']);
})->expectExceptionObject(new PDOException('SQLSTATE[42710]: Duplicate object: 7 ERROR:  type "test_enum" already exists', 42710));

test('create same enum twice using createIfNotExists', function () {
    Schema::createEnum('test_enum', ['one', 'two', 'three']);
    Schema::createEnumIfNotExists('test_enum', ['one', 'two', 'three']);
})->throwsNoExceptions();

test('add new value to existing enum', function () {
    Schema::createEnum('test_enum', ['one', 'two', 'three']);
    assertEnumExists('test_enum');
    assertEnumHasValues('test_enum', ['one', 'two', 'three']);
    Schema::addEnumValue('test_enum', 'four');
    assertEnumHasValues('test_enum', ['one', 'two', 'three', 'four']);
});

test('add new value to existing enum before', function () {
    Schema::createEnum('test_enum', ['one', 'two', 'three']);
    Schema::addEnumValue('test_enum', 'four', Direction::BEFORE, 'one');

    assertEnumHasValues('test_enum', ['four', 'one', 'two', 'three']);
});

test('add new value to existing enum after', function () {
    Schema::createEnum('test_enum', ['one', 'two', 'three']);
    Schema::addEnumValue('test_enum', 'four', Direction::AFTER, 'one');

    assertEnumHasValues('test_enum', ['one', 'four', 'two', 'three']);
});

test('rename enum value', function () {
    Schema::createEnum('test_enum', ['one', 'two', 'three']);
    Schema::renameEnumValue('test_enum', 'one', 'newName');
    assertEnumHasValues('test_enum', ['newName', 'two', 'three']);
});
