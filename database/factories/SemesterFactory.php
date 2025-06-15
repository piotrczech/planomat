<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Enums\SemesterSeasonEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Models\Semester>
 */
class SemesterFactory extends Factory
{
    public function definition(): array
    {
        $semesterStartDate = $this->faker->dateTime();
        $sessionStartDate = $this->faker->dateTimeBetween(
            $semesterStartDate,
            (clone $semesterStartDate)->modify('+3 months'),
        );
        $semesterEndDate = $this->faker->dateTimeBetween(
            $sessionStartDate,
            (clone $sessionStartDate)->modify('+1 months'),
        );

        return [
            'start_year' => (int) $semesterStartDate->format('Y'),
            'season' => $this->faker->randomElement(SemesterSeasonEnum::values()),
            'semester_start_date' => $semesterStartDate,
            'session_start_date' => $sessionStartDate,
            'end_date' => $semesterEndDate,
        ];
    }
}
