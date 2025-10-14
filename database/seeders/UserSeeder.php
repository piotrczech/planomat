<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->scientificWorker()->create([
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'jan.kowalski@pwr.edu.pl',
            'password' => 'password',
        ]);
        User::factory()->scientificWorker()->create([
            'first_name' => 'Piotr',
            'last_name' => 'Nowak',
            'email' => 'piotr.nowak@pwr.edu.pl',
            'password' => 'password',
        ]);
        User::factory()->scientificWorker()->create([
            'first_name' => 'Martyna',
            'last_name' => 'Nowak',
            'email' => 'martyna.nowak@pwr.edu.pl',
            'password' => 'password',
        ]);

        User::factory()->deanOfficeWorker()->create([
            'first_name' => 'Pracownik',
            'last_name' => 'Dziekanatu Testowy',
            'email' => 'dziekanat@pwr.edu.pl',
            'password' => 'password',
        ]);
        User::factory()->administrator()->create([
            'first_name' => 'Administrator',
            'last_name' => 'Testowy',
            'email' => 'admin@pwr.edu.pl',
            'password' => 'password',
        ]);
    }
}
