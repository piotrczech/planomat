<?php

declare(strict_types=1);

namespace Modules\Consultation\Http\Livewire\Dashboard\Components;

use App\Enums\WeekTypeEnum;
use Livewire\Component;

class CalendarAppointment extends Component
{
    public string $time = '';

    public string $location = '';

    public WeekTypeEnum $weekType;

    public function render()
    {
        return view('consultation::dashboard.calendar-appointment');
    }
}
