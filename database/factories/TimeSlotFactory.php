<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeSlot>
 */
class TimeSlotFactory extends Factory
{
    protected $model = TimeSlot::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('07:30', '18:55');
        $end = clone $start;
        $end->modify('+90 minutes');

        return [
            'start_time' => $start,
            'end_time' => $end,
        ];
    }
}
