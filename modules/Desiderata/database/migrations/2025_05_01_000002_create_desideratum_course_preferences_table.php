<?php

declare(strict_types=1);

use App\Enums\CoursePreferenceTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desideratum_course_preferences', function (Blueprint $table): void {
            $table->unsignedSmallInteger('desideratum_id');
            $table->foreign('desideratum_id')
                ->references('id')
                ->on('desiderata')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedTinyInteger('course_id');
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('type', CoursePreferenceTypeEnum::values());

            $table->timestamp('updated_at')
                ->nullable()
                ->useCurrentOnUpdate()
                ->useCurrent();

            $table->primary(['desideratum_id', 'course_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desideratum_course_preferences');
    }
};
