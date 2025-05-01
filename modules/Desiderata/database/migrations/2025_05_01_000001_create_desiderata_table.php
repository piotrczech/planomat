<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desiderata', function (Blueprint $table): void {
            $table->smallIncrements('id');

            $table->unsignedTinyInteger('semester_id');
            $table->foreign('semester_id')
                ->references('id')
                ->on('semesters')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedSmallInteger('scientific_worker_id');
            $table->foreign('scientific_worker_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique(['semester_id', 'scientific_worker_id']);

            $table->boolean('want_stationary')->default(true);
            $table->boolean('want_non_stationary')->default(false);
            $table->boolean('agree_to_overtime')->default(false);

            $table->tinyInteger('master_theses_count')->unsigned()->default(0);
            $table->tinyInteger('bachelor_theses_count')->unsigned()->default(0);

            $table->tinyInteger('max_hours_per_day')->unsigned();
            $table->tinyInteger('max_consecutive_hours')->unsigned();

            $table->text('additional_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desiderata');
    }
};
