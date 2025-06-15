<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Enums\SemesterSeasonEnum;
use App\Infrastructure\Models\Semester;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $season = $now->month >= 8 ? SemesterSeasonEnum::WINTER : SemesterSeasonEnum::SPRING;

        Semester::create([
            'start_year' => $now->year,
            'season' => $season,
            'semester_start_date' => $season === SemesterSeasonEnum::WINTER
                ? Carbon::create($now->year, 10, 1)
                : Carbon::create($now->year, 3, 1),
            'session_start_date' => $season === SemesterSeasonEnum::WINTER
                ? Carbon::create($now->year + 1, 1, 31)
                : Carbon::create($now->year, 6, 21),
            'end_date' => $season === SemesterSeasonEnum::WINTER
                ? Carbon::create($now->year + 1, 2, 21)
                : Carbon::create($now->year, 7, 14),
        ]);
    }
}
