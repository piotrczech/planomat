<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Domain\Enums\SemesterSeasonEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table): void {
            $table->tinyIncrements('id');
            $table->year('start_year')->index();
            $table->enum('season', SemesterSeasonEnum::values());
            $table->date('semester_start_date');
            $table->date('session_start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->unique(['start_year', 'season']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
