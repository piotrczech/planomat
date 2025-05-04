<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Livewire\Desideratum\ScientificWorker;

use App\Enums\WeekdayEnum;
use Spatie\LivewireWizard\Components\StepComponent;

class DesiderataFormAvailabilityStepComponent extends StepComponent
{
    public array $unavailableTimeSlots = [];

    public int $selectedSlotsCount = 0;

    public int $maxUnavailableSlots = 5;

    public function mount(): void
    {
        $this->initTimeSlots();
    }

    public function render()
    {
        return view('desiderata::components.desideratum.form-wizard.desiderata-form-availability-step-component');
    }

    private function initTimeSlots(): void
    {
        // Przykładowe przedziały czasowe (docelowo z bazy)
        $timeSlots = [
            ['id' => 1, 'range' => '7:30-9:00'],
            ['id' => 2, 'range' => '9:15-11:00'],
            ['id' => 3, 'range' => '11:15-13:00'],
            ['id' => 4, 'range' => '13:15-15:00'],
            ['id' => 5, 'range' => '15:15-16:55'],
            ['id' => 6, 'range' => '17:05-18:45'],
            ['id' => 7, 'range' => '18:55-20:35'],
        ];

        // Inicjalizacja tablicy niedostępnych slotów
        foreach (WeekdayEnum::cases() as $day) {
            $this->unavailableTimeSlots[$day->value] = [];

            foreach ($timeSlots as $slot) {
                $this->unavailableTimeSlots[$day->value][$slot['id']] = [
                    'selected' => false,
                    'range' => $slot['range'],
                ];
            }
        }
    }

    public function toggleTimeSlot(string $day, int $slotId): void
    {
        $isCurrentlySelected = $this->unavailableTimeSlots[$day][$slotId]['selected'];

        // Jeśli próbujemy odznaczyć - po prostu zmieniamy stan
        if ($isCurrentlySelected) {
            $this->unavailableTimeSlots[$day][$slotId]['selected'] = false;
            $this->selectedSlotsCount--;
        }
        // Jeśli próbujemy zaznaczyć - sprawdzamy limit
        elseif ($this->selectedSlotsCount < $this->maxUnavailableSlots) {
            $this->unavailableTimeSlots[$day][$slotId]['selected'] = true;
            $this->selectedSlotsCount++;
        }
    }
}
