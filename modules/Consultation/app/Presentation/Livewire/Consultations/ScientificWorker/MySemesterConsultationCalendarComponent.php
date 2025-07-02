<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use App\Domain\Enums\SemesterSeasonEnum;
use Livewire\Component;
use Illuminate\Support\Facades\App;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetSemesterConsultationsUseCase;
use Modules\Consultation\Application\UseCases\ScientificWorker\DeleteSemesterConsultationUseCase;
use Exception;
use Livewire\Attributes\On;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetConsultationSummaryTimeUseCase;

class MySemesterConsultationCalendarComponent extends Component
{
    public array $consultationEvents = [];

    public ?string $consultationSummaryTime = null;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public string $title = '';

    public function mount(GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase): void
    {
        $semester = $getActiveConsultationSemesterUseCase->execute();

        if ($semester) {
            $seasonName = $semester->season === SemesterSeasonEnum::WINTER ? __('consultation::consultation.in_semester_winter') : __('consultation::consultation.in_semester_summer');
            $this->title = __('consultation::consultation.my_semester_consultations_title', [
                'season' => $seasonName,
                'academic_year' => $semester->academic_year,
            ]);
        } else {
            $this->title = __('consultation::consultation.My Semester Consultation');
        }

        $this->loadInitialData(
            App::make(GetSemesterConsultationsUseCase::class),
            App::make(GetConsultationSummaryTimeUseCase::class),
        );
    }

    #[On('consultationSaved')]
    public function loadInitialData(
        GetSemesterConsultationsUseCase $getSemesterConsultationsUseCase,
        GetConsultationSummaryTimeUseCase $getConsultationSummaryTime,
    ): void {
        $events = $getSemesterConsultationsUseCase->execute();
        $this->consultationSummaryTime = $getConsultationSummaryTime->execute();

        $groupedEvents = [];

        foreach ($events as $event) {
            $groupedEvents[$event['weekday']][] = $event;
        }

        $this->consultationEvents = $groupedEvents;
    }

    public function removeConsultation(int $eventId): void
    {
        try {
            $useCase = App::make(DeleteSemesterConsultationUseCase::class);
            $result = $useCase->execute($eventId);

            if ($result) {
                foreach ($this->consultationEvents as $day => $events) {
                    $this->consultationEvents[$day] = array_filter(
                        $events,
                        fn ($event) => $event['id'] !== $eventId,
                    );
                }

                $this->successMessage = __('consultation::consultation.Consultation successfully deleted');
                $this->loadInitialData(
                    App::make(GetSemesterConsultationsUseCase::class),
                    App::make(GetConsultationSummaryTimeUseCase::class),
                );
                $this->dispatch('consultationDeleted');
            } else {
                $this->errorMessage = __('consultation::consultation.Failed to delete consultation');
            }
        } catch (Exception $e) {
            $this->errorMessage = __('consultation::consultation.Error: :message', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.my-semester-consultation-calendar-component');
    }
}
