<?php

declare(strict_types=1);

namespace Modules\Desiderata\Database\Factories;

use App\Enums\CoursePreferenceTypeEnum;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Desiderata\Models\Desideratum;
use Modules\Desiderata\Models\DesideratumCoursePreference;

class DesideratumCoursePreferenceFactory extends Factory
{
    protected $model = DesideratumCoursePreference::class;

    public function definition(): array
    {
        return [
            'desideratum_id' => Desideratum::inRandomOrder()->first() ?? Desideratum::factory(),
            'course_id' => Course::inRandomOrder()->first() ?? Course::factory(),
            'type' => $this->faker->randomElement(CoursePreferenceTypeEnum::values()),
        ];
    }

    public function can(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => CoursePreferenceTypeEnum::CAN,
            ];
        });
    }

    public function want(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => CoursePreferenceTypeEnum::WANT,
            ];
        });
    }

    public function notWant(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => CoursePreferenceTypeEnum::NOT_WANT,
            ];
        });
    }
}
