<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'active_semester_for_consultations_id'],
            ['value' => null],
        );

        Setting::updateOrCreate(
            ['key' => 'active_semester_for_desiderata_id'],
            ['value' => null],
        );
    }
}
