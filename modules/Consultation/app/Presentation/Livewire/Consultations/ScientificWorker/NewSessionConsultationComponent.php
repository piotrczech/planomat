<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use Livewire\Component;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;
use Exception;

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
        // Używamy standardowej walidacji Livewire, która automatycznie wyświetli błędy pod polami
        $validatedData = $this->validate();

        try {
            // Utworzenie DTO z zwalidowanych danych
            $requestData = CreateNewSessionConsultationDto::from($validatedData);

            // TO DO: tutaj powinno być wywołanie odpowiedniego UseCase do zapisania konsultacji
            // np. $useCase->execute($requestData);

            // Powiadomienie o sukcesie
            $this->successMessage = __('consultation::consultation.Successfully created consultation session');
            $this->resetForm();

        } catch (Exception $e) {
            // Ten kod będzie uruchamiany tylko w przypadku innych błędów niż walidacja
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
