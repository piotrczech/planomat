<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use App\Application\Semester\UseCases\GetCurrentSemesterDatesUseCase;
use Livewire\Component;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;
use Exception;
use Illuminate\Support\Facades\App;
use Modules\Consultation\Application\UseCases\ScientificWorker\CreateNewSessionConsultationUseCase;

class NewSessionConsultationComponent extends Component
{
    public string $consultationDate = '';

    public string $consultationStartTime = '';

    public string $consultationEndTime = '';

    public string $consultationLocation = '';

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public string $startSessionDate = '';

    public string $endSessionDate = '';

    protected function rules()
    {
        return CreateNewSessionConsultationDto::rules();
    }

    protected function messages()
    {
        return CreateNewSessionConsultationDto::messages();
    }

    public function fetchSessionDates(): void
    {
        $useCase = App::make(GetCurrentSemesterDatesUseCase::class);
        $dates = $useCase->execute();

        if ($dates) {
            $this->startSessionDate = $dates['session_start_date'];
            $this->endSessionDate = $dates['end_date'];
        } else {
            $this->startSessionDate = '';
            $this->endSessionDate = '';
            $this->errorMessage = __('consultation::consultation.No current semester found.');
        }
    }

    public function mount(): void
    {
        $this->resetForm();
        $this->fetchSessionDates();
    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.new-session-consultation-component');
    }

    public function addConsultation(): void
    {
        $validatedData = $this->validate();

        try {
            $requestData = CreateNewSessionConsultationDto::from($validatedData);

            $useCase = App::make(CreateNewSessionConsultationUseCase::class);
            $useCase->execute($requestData);

            $this->successMessage = __('consultation::consultation.Successfully created consultation session');
            $this->dispatch('consultationSaved');
            $this->resetForm();
        } catch (Exception $e) {
            $this->errorMessage = __('consultation::consultation.Error: :message', ['message' => $e->getMessage()]);
        }
    }

    private function resetForm(): void
    {
        $this->consultationDate = '';
        $this->consultationStartTime = '';
        $this->consultationEndTime = '';
        $this->consultationLocation = '';
    }
}
