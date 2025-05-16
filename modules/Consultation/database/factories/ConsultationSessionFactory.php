<?php

declare(strict_types=1);

namespace Modules\Consultation\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Consultation\Infrastructure\Models\ConsultationSemester;
use Modules\Consultation\Infrastructure\Models\ConsultationSession;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Consultation\Infrastructure\Models\ConsultationSession>
 */
class ConsultationSessionFactory extends Factory
{
    protected $model = ConsultationSession::class;

    public function definition(): array
    {
        $startTime = Carbon::createFromTime(
            $this->faker->numberBetween(7, 19),
            $this->faker->randomElement([0, 15, 30, 45]),
        );

        $duration = $this->faker->numberBetween(60, 180);
        $endTime = (clone $startTime)->addMinutes($duration);

        return [
            'consultation_semester_id' => ConsultationSemester::inRandomOrder()->first() ?? ConsultationSemester::factory(),
            'consultation_date' => $this->faker->date(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => $this->faker->address,
        ];
    }
}
