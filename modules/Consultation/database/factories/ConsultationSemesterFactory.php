<?php

declare(strict_types=1);

namespace Modules\Consultation\Database\Factories;

use App\Enums\WeekdayEnum;
use App\Enums\WeekTypeEnum;
use App\Models\Semester;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Consultation\Infrastructure\Models\ConsultationSemester;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Consultation\Infrastructure\Models\ConsultationSemester>
 */
class ConsultationSemesterFactory extends Factory
{
    protected $model = ConsultationSemester::class;

    public function definition(): array
    {
        $startTime = Carbon::createFromTime(
            $this->faker->numberBetween(7, 19),
            $this->faker->randomElement([0, 15, 30, 45]),
        );

        $duration = $this->faker->numberBetween(60, 180);
        $endTime = (clone $startTime)->addMinutes($duration);

        return [
            'scientific_worker_id' => User::inRandomOrder()->first() ?? User::factory(),
            'semester_id' => Semester::inRandomOrder()->first() ?? Semester::factory(),
            'day' => $this->faker->randomElement(WeekdayEnum::values()),
            'week_type' => $this->faker->randomElement(WeekTypeEnum::values()),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => $this->faker->address(),
        ];
    }

    public function everyWeek(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'week_type' => WeekTypeEnum::ALL,
            ];
        });
    }

    public function oddWeek(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'week_type' => WeekTypeEnum::ODD,
            ];
        });
    }

    public function evenWeek(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'week_type' => WeekTypeEnum::EVEN,
            ];
        });
    }

    public function weekday(WeekdayEnum $day): Factory
    {
        return $this->state(function (array $attributes) use ($day) {
            return [
                'day' => $day,
            ];
        });
    }
}
