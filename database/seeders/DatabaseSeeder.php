<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TimeSlotSeeder::class,
            CourseSeeder::class,
            SemesterSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
        ]);
    }
}
