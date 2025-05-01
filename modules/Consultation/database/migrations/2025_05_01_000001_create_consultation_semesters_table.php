<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_semesters', function (Blueprint $table): void {
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

            $table->enum('day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->enum('week_type', ['all', 'odd', 'even'])->default('all');

            $table->time('start_time');
            $table->time('end_time');

            $table->string('location', 100);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_semesters');
    }
};
