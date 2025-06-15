<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courseNames = [
            'Analiza matematyczna 1',
            'Analiza matematyczna 2',
            'Algebra',
            'Równania różniczkowe',
            'Rachunek prawdopodobieństwa',
            'Statystyka',
        ];

        foreach ($courseNames as $courseName) {
            Course::create([
                'name' => $courseName,
            ]);
        }
    }
}
