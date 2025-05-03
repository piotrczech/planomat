<?php

declare(strict_types=1);

namespace Modules\Consultation\Http\Livewire\Dashboard\Components;

use App\Enums\WeekdayEnum;
use Livewire\Component;

class CalendarDay extends Component
{
    public array $appointments = [];

    public WeekdayEnum $day;

    public function render()
    {
        return view('consultation::dashboard.calendar-day');
    }
}
