<?php

declare(strict_types=1);

namespace Modules\Consultation\Http\Livewire\Dashboard;

use App\Enums\WeekdayEnum;
use App\Enums\WeekTypeEnum;
use Livewire\Component;

class ConsultationsCard extends Component
{
    public array $consultations = [];

    public function mount(): void
    {
        $this->consultations = [
            [
                'id' => 1,
                'day' => WeekdayEnum::FRIDAY,
                'time' => '10:00 - 12:00',
                'location' => 'Pokój 234',
                'status' => 'completed',
                'week_type' => WeekTypeEnum::ALL,
            ],
            [
                'id' => 2,
                'day' => WeekdayEnum::TUESDAY,
                'time' => '14:00 - 16:00',
                'location' => 'Online (MS Teams)',
                'status' => 'pending',
                'week_type' => WeekTypeEnum::ALL,
            ],
            [
                'id' => 3,
                'day' => WeekdayEnum::THURSDAY,
                'time' => '11:00 - 13:00',
                'location' => 'Pokój 234',
                'status' => 'pending',
                'week_type' => WeekTypeEnum::EVEN,
            ],
        ];
    }

    public function redirectToForm()
    {
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('consultation::dashboard.consultations-card');
    }

    public function getStatusColor($status)
    {
        return match ($status) {
            'completed' => 'emerald',
            'pending' => 'yellow',
        };
    }
}
