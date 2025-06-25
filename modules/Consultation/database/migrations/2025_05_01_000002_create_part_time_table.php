<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('part_time_consultations', function (Blueprint $table): void {
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

            $table->date('consultation_date');

            $table->time('start_time');
            $table->time('end_time');

            $table->string('location_building', 100);
            $table->string('location_room', 100)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_time_consultations');
    }
};
