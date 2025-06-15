<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use Livewire\Component;
use App\Domain\Enums\WeekdayEnum;
use App\Domain\Enums\WeekTypeEnum;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Modules\Consultation\Application\UseCases\ScientificWorker\CreateNewSemesterConsultationUseCase;
use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Exception;

class NewSemesterConsultationComponent extends Component
{
    public string $consultationWeekday;

    public string $dailyConsultationWeekType;

    public string $weeklyConsultationDates;

    public string $consultationStartTime;

    public string $consultationEndTime;

    public string $consultationLocation;

    public bool $isAddingConsultation = false;

    public string $successMessage = '';

    public function mount(): void
    {
        $this->resetForm();
    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.new-semester-consultation-component');
    }

    public function addConsultation(): void
    {
        $this->isAddingConsultation = true;

        try {
            $data = [
                'consultationWeekday' => $this->consultationWeekday,
                'dailyConsultationWeekType' => $this->dailyConsultationWeekType,
                'weeklyConsultationDates' => $this->weeklyConsultationDates,
                'consultationStartTime' => $this->consultationStartTime,
                'consultationEndTime' => $this->consultationEndTime,
                'consultationLocation' => $this->consultationLocation,
            ];

            $dto = CreateNewSemesterConsultationDto::validateAndCreate($data);

            $useCase = App::make(CreateNewSemesterConsultationUseCase::class);
            $createdCount = $useCase->execute($dto);

            $this->resetForm();

            $this->dispatch('consultationSaved', [
                'count' => $createdCount,
            ]);
        } catch (ValidationException $e) {
            $this->isAddingConsultation = false;

            $this->setErrorBag($e->validator->getMessageBag());
        } catch (Exception $e) {
            $this->isAddingConsultation = false;

            $this->addError('consultationWeekday', $e->getMessage());
        }
    }

    private function resetForm(): void
    {
        $this->consultationWeekday = WeekdayEnum::MONDAY->value;
        $this->dailyConsultationWeekType = WeekTypeEnum::ALL->value;
        $this->weeklyConsultationDates = '';
        $this->consultationStartTime = '';
        $this->consultationEndTime = '';
        $this->consultationLocation = '';
        $this->isAddingConsultation = false;
        $this->resetErrorBag();
        $this->successMessage = '';
    }
}
