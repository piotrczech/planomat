<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

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

    protected function rules()
    {
        return CreateNewSessionConsultationDto::rules();
    }

    protected function messages()
    {
        return CreateNewSessionConsultationDto::messages();
    }

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
