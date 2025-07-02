<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Modules\Consultation\Domain\Dto\CreateNewPartTimeConsultationDto;
use Exception;
use Illuminate\Support\Facades\App;
use Modules\Consultation\Application\UseCases\ScientificWorker\CreateNewPartTimeConsultationUseCase;

class NewPartTimeConsultationComponent extends Component
{
    public string $consultationDate = '';

    public string $consultationStartTime = '';

    public string $consultationEndTime = '';

    public string $consultationLocationBuilding = '';

    public ?string $consultationLocationRoom = null;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public string $startSemesterDate = '';

    public string $startSessionDate = '';

    public function fetchPartTimeDates(): void
    {
        $useCase = App::make(GetActiveConsultationSemesterUseCase::class);
        $semester = $useCase->execute();

        if ($semester) {
            $this->startSemesterDate = $semester->semester_start_date->toDateString();
            $this->startSessionDate = $semester->session_start_date->toDateString();
        } else {
            $this->startSemesterDate = '';
            $this->startSessionDate = '';
            $this->errorMessage = __('consultation::consultation.No current semester found.');
        }
    }

    public function mount(): void
    {
        $this->resetForm();
        $this->fetchPartTimeDates();
    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.new-part-time-consultation-component');
    }

    public function addConsultation(): void
    {
        $data = [
            'consultationDate' => $this->consultationDate,
            'consultationStartTime' => $this->consultationStartTime,
            'consultationEndTime' => $this->consultationEndTime,
            'consultationLocationBuilding' => $this->consultationLocationBuilding,
            'consultationLocationRoom' => $this->consultationLocationRoom,
        ];

        try {
            $requestData = CreateNewPartTimeConsultationDto::validateAndCreate($data);

            $useCase = App::make(CreateNewPartTimeConsultationUseCase::class);
            $useCase->execute($requestData);

            $this->successMessage = __('consultation::consultation.Successfully created part-time consultation');
            $this->dispatch('consultationSaved');
            $this->resetForm();
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
        } catch (Exception $e) {
            $this->errorMessage = __('consultation::consultation.Error: :message', ['message' => $e->getMessage()]);
        }
    }

    public function resetForm(): void
    {
        $this->consultationDate = '';
        $this->consultationStartTime = '';
        $this->consultationEndTime = '';
        $this->consultationLocationBuilding = '';
        $this->consultationLocationRoom = null;
    }
}
