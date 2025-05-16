<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use Livewire\Component;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;

class NewSessionConsultationComponent extends Component
{
    public string $consultationDate;

    public string $consultationStartTime;

    public string $consultationEndTime;

    public string $consultationLocation;

    public function mount(): void
    {
        $this->resetForm();
    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.new-session-consultation-component');
    }

    public function addConsultation(): void
    {
        $requestData = CreateNewSessionConsultationDto::from([
            'consultationDate' => $this->consultationDate,
            'consultationStartTime' => $this->consultationStartTime,
            'consultationEndTime' => $this->consultationEndTime,
            'consultationLocation' => $this->consultationLocation,
        ]);

        dd($requestData->toArray());
    }

    private function resetForm(): void
    {
        $this->consultationDate = '';
        $this->consultationStartTime = '';
        $this->consultationEndTime = '';
        $this->consultationLocation = '';
    }
}
