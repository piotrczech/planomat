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

        Setting::updateOrCreate(
            ['key' => 'notifications_semester_consultations_enabled'],
            ['value' => '1'],
        );

        Setting::updateOrCreate(
            ['key' => 'notifications_semester_consultations_days_after'],
            ['value' => '21'],
        );

        Setting::updateOrCreate(
            ['key' => 'notifications_desiderata_enabled'],
            ['value' => '1'],
        );

        Setting::updateOrCreate(
            ['key' => 'notifications_desiderata_days_after'],
            ['value' => '21'],
        );

        Setting::updateOrCreate(
            ['key' => 'notifications_session_consultations_enabled'],
            ['value' => '1'],
        );

        Setting::updateOrCreate(
            ['key' => 'notifications_session_consultations_days_after'],
            ['value' => '0'],
        );

        Setting::updateOrCreate(
            ['key' => 'notifications_weekly_semester_summary_enabled'],
            ['value' => '1'],
        );
    }
}
