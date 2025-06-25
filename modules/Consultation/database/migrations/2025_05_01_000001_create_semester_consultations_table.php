<?php

declare(strict_types=1);

use App\Domain\Enums\WeekdayEnum;
use App\Domain\Enums\WeekTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester_consultations', function (Blueprint $table): void {
            $table->mediumIncrements('id');

            $table->unsignedSmallInteger('scientific_worker_id');
            $table->foreign('scientific_worker_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('semester_id');
            $table->foreign('semester_id')
                ->references('id')
                ->on('semesters')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('day', WeekdayEnum::values(includeWeekend: false));
            $table->enum('week_type', WeekTypeEnum::values())->nullable();

            $table->time('start_time');
            $table->time('end_time');

            $table->string('location_building', 100);
            $table->string('location_room', 100)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_consultations');
    }
};
