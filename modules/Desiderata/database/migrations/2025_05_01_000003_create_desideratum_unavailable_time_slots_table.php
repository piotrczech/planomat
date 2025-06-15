<?php

declare(strict_types=1);

use App\Domain\Enums\WeekdayEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desideratum_unavailable_time_slots', function (Blueprint $table): void {
            $table->unsignedSmallInteger('desideratum_id');
            $table->foreign('desideratum_id')
                ->references('id')
                ->on('desiderata')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('day', WeekdayEnum::values());
            $table->smallInteger('time_slot_id')
                ->constrained('time_slots')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamp('updated_at')
                ->nullable()
                ->useCurrentOnUpdate()
                ->useCurrent();

            $table->primary(['desideratum_id', 'day', 'time_slot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desideratum_unavailable_time_slots');
    }
};
