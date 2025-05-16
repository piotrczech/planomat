<?php

declare(strict_types=1);

namespace Modules\Desiderata\Database\Factories;

use App\Enums\WeekdayEnum;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Desiderata\Infrastructure\Models\Desideratum;
use Modules\Desiderata\Infrastructure\Models\DesideratumUnavailableTimeSlot;

class DesideratumUnavailableTimeSlotFactory extends Factory
{
    protected $model = DesideratumUnavailableTimeSlot::class;

    public function definition(): array
    {
        return [
            'desideratum_id' => Desideratum::inRandomOrder()->first() ?? Desideratum::factory(),
            'day' => $this->faker->randomElement(WeekdayEnum::values()),
            'time_slot_id' => TimeSlot::inRandomOrder()->first() ?? TimeSlot::factory(),
        ];
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
