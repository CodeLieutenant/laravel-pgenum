<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workbench\App\Enums\CustomBackedIntEnum;
use Workbench\App\Enums\CustomBackedStringEnum;
use Workbench\App\Enums\CustomNonBackedEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::createEnumIfNotExists(CustomBackedStringEnum::class);
        Schema::createEnumIfNotExists(CustomBackedIntEnum::class);
        Schema::createEnumIfNotExists(CustomNonBackedEnum::class);

        Schema::create('customs', function (Blueprint $table) {
            $table->id();

            $table->enumeration('string_backed_enum', CustomBackedStringEnum::class);
            $table->enumeration('another_string_backed_enum', CustomBackedStringEnum::class);
            $table->enumeration('int_backed_enum', CustomBackedIntEnum::class);
            $table->enumeration('non_backed_enum', CustomNonBackedEnum::class);

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customs');

        Schema::dropEnumIfExists(CustomBackedStringEnum::class);
        Schema::dropEnumIfExists(CustomBackedIntEnum::class);
        Schema::dropEnumIfExists(CustomNonBackedEnum::class);
    }
};
