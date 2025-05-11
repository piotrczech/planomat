<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use Livewire\Component;

class MySemesterConsultationCalendarComponent extends Component
{
    public array $consultationEvents = [];

    public function mount(): void
    {
        // Przykładowe wydarzenia konsultacji (weekday to liczby 0-6, gdzie 0=poniedziałek)
        $this->consultationEvents = [
            [
                'id' => 1,
                'weekday' => 0, // Poniedziałek
                'startTime' => '09:05',
                'endTime' => '10:30',
                'location' => 'Sala 204, Budynek Główny',
                'weekType' => 'every',
            ],
            [
                'id' => 2,
                'weekday' => 2, // Środa
                'startTime' => '13:15',
                'endTime' => '14:45',
                'location' => 'Sala konferencyjna',
                'weekType' => 'even',
            ],
            [
                'id' => 3,
                'weekday' => 3, // Czwartek
                'startTime' => '16:00',
                'endTime' => '17:30',
                'location' => 'Gabinet 312',
                'weekType' => 'odd',
            ],
        ];
    }

    public function removeConsultation(int $eventId): void
    {
        $this->consultationEvents = array_filter(
            $this->consultationEvents,
            fn ($event) => $event['id'] !== $eventId,
        );
    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.my-semester-consultation-calendar-component');
    }
}
