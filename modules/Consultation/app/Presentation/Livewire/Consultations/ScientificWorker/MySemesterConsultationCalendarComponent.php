<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use App\Domain\Enums\SemesterSeasonEnum;
use Livewire\Component;
use Illuminate\Support\Facades\App;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetSemesterConsultationsUseCase;
use Modules\Consultation\Application\UseCases\ScientificWorker\DeleteSemesterConsultationUseCase;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetOtherScientificWorkerConsultationsUseCase;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetScientificWorkersListUseCase;
use Exception;
use Livewire\Attributes\On;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetConsultationSummaryTimeUseCase;

class MySemesterConsultationCalendarComponent extends Component
{
    public array $consultationEvents = [];

    public array $otherWorkerConsultationEvents = [];

    public array $scientificWorkers = [];

    public ?string $consultationSummaryTime = null;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public string $title = '';

    public ?int $selectedWorkerId = null;

    public ?string $selectedWorkerName = null;

    public bool $showComparison = false;

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

        $this->loadScientificWorkers();
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

    private function loadScientificWorkers(): void
    {
        $useCase = App::make(GetScientificWorkersListUseCase::class);
        $workers = $useCase->execute();

        $this->scientificWorkers = $workers->map(function ($worker) {
            return [
                'id' => $worker->id,
                'name' => $worker->fullName(),
                'academic_title' => $worker->academic_title,
            ];
        })->toArray();
    }

    public function selectWorker($workerId): void
    {
        if (empty($workerId) || $workerId === '' || $workerId === null) {
            $this->clearComparison();

            return;
        }

        $this->selectedWorkerId = (int) $workerId;

        $selectedWorker = collect($this->scientificWorkers)
            ->firstWhere('id', $this->selectedWorkerId);
        $this->selectedWorkerName = $selectedWorker['name'] ?? null;

        $this->loadOtherWorkerConsultations();
    }

    private function loadOtherWorkerConsultations(): void
    {
        if (!$this->selectedWorkerId) {
            return;
        }

        $useCase = App::make(GetOtherScientificWorkerConsultationsUseCase::class);
        $events = $useCase->execute($this->selectedWorkerId);

        $groupedEvents = [];

        foreach ($events as $event) {
            $groupedEvents[$event['weekday']][] = $event;
        }

        $this->otherWorkerConsultationEvents = $groupedEvents;
        $this->showComparison = true;
    }

    public function toggleComparison(): void
    {
        $this->showComparison = !$this->showComparison;
    }

    public function clearComparison(): void
    {
        $this->selectedWorkerId = null;
        $this->selectedWorkerName = null;
        $this->otherWorkerConsultationEvents = [];
        $this->showComparison = false;
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
