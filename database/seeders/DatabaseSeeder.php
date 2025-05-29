<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TimeSlotSeeder::class,
            CoursesSeeder::class,
        ]);

        User::factory()->scientificWorker()->create([
            'name' => 'Jan Kowalski',
            'email' => 'jan.kowalski@pwr.edu.pl',
            'password' => 'password',
        ]);
        User::factory()->scientificWorker()->create([
            'name' => 'Piotr Nowak',
            'email' => 'piotr.nowak@pwr.edu.pl',
            'password' => 'password',
        ]);
        User::factory()->scientificWorker()->create([
            'name' => 'Martyna Nowak',
            'email' => 'martyna.nowak@pwr.edu.pl',
            'password' => 'password',
        ]);

        User::factory()->deanOfficeWorker()->create([
            'name' => 'Pracownik Dziekanatu Testowy',
            'email' => 'dziekanat@pwr.edu.pl',
            'password' => 'password',
        ]);
        User::factory()->administrator()->create([
            'name' => 'Administrator Testowy',
            'email' => 'admin@pwr.edu.pl',
            'password' => 'password',
        ]);
    }
}
