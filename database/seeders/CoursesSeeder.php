<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run(): void
    {
        $courseNames = [
            'name' => 'Analiza matematyczna 1',
            'name' => 'Analiza matematyczna 2',
            'name' => 'Algebra',
            'name' => 'Równania różniczkowe',
            'name' => 'Rachunek prawdopodobieństwa',
            'name' => 'Statystyka',
        ];

        foreach ($courseNames as $courseName) {
            Course::create([
                'name' => $courseName,
            ]);
        }
    }
}
