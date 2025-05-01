<?php

declare(strict_types=1);

namespace Modules\Desiderata\Database\Factories;

use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Desiderata\Models\Desideratum;

class DesideratumFactory extends Factory
{
    protected $model = Desideratum::class;

    public function definition(): array
    {
        return [
            'semester_id' => Semester::inRandomOrder()->first() ?? Semester::factory(),
            'scientific_worker_id' => User::inRandomOrder()->first() ?? User::factory(),

            'want_stationary' => $this->faker->boolean(80),
            'want_non_stationary' => $this->faker->boolean(40),
            'agree_to_overtime' => $this->faker->boolean(60),

            'master_theses_count' => $this->faker->numberBetween(0, 10),
            'bachelor_theses_count' => $this->faker->numberBetween(0, 15),

            'max_hours_per_day' => $this->faker->numberBetween(4, 10),
            'max_consecutive_hours' => $this->faker->numberBetween(2, 6),

            'additional_notes' => $this->faker->optional(0.7)->realText(80),
        ];
    }
}
