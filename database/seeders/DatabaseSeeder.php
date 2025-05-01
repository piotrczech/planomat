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
        ]);

        User::factory()->scientificWorker()->create([
            'name' => 'Naukowiec Testowy',
            'email' => 'prowadzacy@pwr.edu.pl',
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
