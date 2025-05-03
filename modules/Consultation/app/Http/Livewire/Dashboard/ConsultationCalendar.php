<?php

declare(strict_types=1);

namespace Modules\Consultation\Http\Livewire\Dashboard;

use App\Enums\WeekdayEnum;
use App\Enums\WeekTypeEnum;
use Livewire\Component;

class ConsultationCalendar extends Component
{
    public array $calendarDays = [];

    public string $currentMonth = '';

    public function mount(): void
    {
        $this->currentMonth = 'Maj 2025';
        $this->calendarDays = [
            [
                'day' => WeekdayEnum::MONDAY,
                'appointments' => [],
            ],
            [
                'day' => WeekdayEnum::TUESDAY,
                'appointments' => [
                    [
                        'time' => '14:00 - 16:00',
                        'location' => 'Online (MS Teams)',
                        'week_type' => WeekTypeEnum::ALL,
                    ],
                ],
            ],
            [
                'day' => WeekdayEnum::WEDNESDAY,
                'appointments' => [],
            ],
            [
                'day' => WeekdayEnum::THURSDAY,
                'appointments' => [
                    [
                        'time' => '11:00 - 13:00',
                        'location' => 'Pokój 234',
                        'week_type' => WeekTypeEnum::EVEN,
                    ],
                ],
            ],
            [
                'day' => WeekdayEnum::FRIDAY,
                'appointments' => [
                    [
                        'time' => '10:00 - 12:00',
                        'location' => 'Pokój 234',
                        'week_type' => WeekTypeEnum::ALL,
                    ],
                ],
            ],
            [
                'day' => WeekdayEnum::SATURDAY,
                'appointments' => [],
            ],
            [
                'day' => WeekdayEnum::SUNDAY,
                'appointments' => [],
            ],
        ];
    }

    public function render()
    {
        return view('consultation::dashboard.consultation-calendar');
    }
}
