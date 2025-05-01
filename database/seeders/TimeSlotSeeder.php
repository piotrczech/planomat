<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        $timeSlots = [
            [
                'start_time' => '07:30:00',
                'end_time' => '09:00:00',
            ],
            [
                'start_time' => '09:15:00',
                'end_time' => '11:00:00',
            ],
            [
                'start_time' => '11:15:00',
                'end_time' => '13:00:00',
            ],
            [
                'start_time' => '13:15:00',
                'end_time' => '15:00:00',
            ],
            [
                'start_time' => '15:15:00',
                'end_time' => '16:55:00',
            ],
            [
                'start_time' => '17:05:00',
                'end_time' => '18:45:00',
            ],
            [
                'start_time' => '18:55:00',
                'end_time' => '20:35:00',
            ],
        ];

        foreach ($timeSlots as $timeSlot) {
            TimeSlot::updateOrCreate(
                [
                    'start_time' => $timeSlot['start_time'],
                    'end_time' => $timeSlot['end_time'],
                ],
            );
        }
    }
}
