<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use App\Application\UseCases\Semester\GetCurrentSemesterDatesUseCase;
use Carbon\Carbon;
use Livewire\Component;
use Exception;
use Illuminate\Support\Facades\App;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetSessionConsultationsUseCase;
use Livewire\Attributes\On;
use Modules\Consultation\Application\UseCases\ScientificWorker\DeleteSessionConsultationUseCase;

class MySessionConsultationCalendarComponent extends Component
{
    public array $consultationEvents = [];

    public Carbon $currentMonth;

    public array $calendarDays = [];

    public ?Carbon $sessionStart = null;

    public ?Carbon $sessionEnd = null;

    public array $availableMonths = [];

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public bool $calendarEnabled = false;

    public function mount(): void
    {
        $semesterDatesUseCase = App::make(GetCurrentSemesterDatesUseCase::class);
        $dates = $semesterDatesUseCase->execute();

        if ($dates && isset($dates['session_start_date'], $dates['end_date'])) {
            $this->sessionStart = Carbon::parse($dates['session_start_date']);
            $this->sessionEnd = Carbon::parse($dates['end_date']);
            $this->calendarEnabled = true;
        } else {
            $this->errorMessage = __('consultation::consultation.No current semester dates found. Calendar functionality may be limited.');
            $this->sessionStart = Carbon::now()->startOfMonth();
            $this->sessionEnd = Carbon::now()->endOfMonth();
            $this->calendarEnabled = false;
        }

        if ($this->calendarEnabled) {
            $this->generateAvailableMonths();
            $this->currentMonth = $this->sessionStart->copy()->startOfMonth();
        } else {
            $this->currentMonth = Carbon::now()->startOfMonth();
        }

        $this->generateCalendarDays();
        $this->loadConsultations();
    }

    public function generateAvailableMonths(): void
    {
        $this->availableMonths = [];

        if (!$this->sessionStart || !$this->sessionEnd) {
            return;
        }

        $startMonth = $this->sessionStart->copy()->startOfMonth();
        $endMonth = $this->sessionEnd->copy()->startOfMonth();

        $currentMonthIterator = $startMonth->copy();

        while ($currentMonthIterator->lte($endMonth)) {
            $this->availableMonths[] = [
                'year' => $currentMonthIterator->year,
                'month' => $currentMonthIterator->month,
                'name' => $currentMonthIterator->translatedFormat('F Y'),
                'carbon' => $currentMonthIterator->copy(),
            ];
            $currentMonthIterator->addMonth();
        }
    }

    public function generateCalendarDays(): void
    {
        $this->calendarDays = [];

        if (!$this->sessionStart || !$this->sessionEnd) {
            return;
        }

        $startOfMonth = $this->currentMonth->copy()->startOfMonth();
        $endOfMonth = $this->currentMonth->copy()->endOfMonth();

        $startDay = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endDay = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $currentDayIterator = $startDay->copy();

        while ($currentDayIterator->lte($endDay)) {
            $dateString = $currentDayIterator->toDateString();
            $isCurrentMonth = $currentDayIterator->month === $this->currentMonth->month;
            $isToday = $currentDayIterator->isToday();
            $isInSessionRange = $this->calendarEnabled && $this->sessionStart && $this->sessionEnd && $currentDayIterator->between($this->sessionStart, $this->sessionEnd);

            $this->calendarDays[] = [
                'date' => $dateString,
                'day' => $currentDayIterator->day,
                'dayOfWeek' => $currentDayIterator->dayOfWeek,
                'isCurrentMonth' => $isCurrentMonth,
                'isToday' => $isToday,
                'isInSessionRange' => $isInSessionRange,
            ];

            $currentDayIterator->addDay();
        }
    }

    #[On('consultationSaved')]
    public function loadConsultations(): void
    {
        $this->consultationEvents = App::make(GetSessionConsultationsUseCase::class)->execute();

        if (empty($this->calendarDays) && ($this->sessionStart && $this->sessionEnd)) {
            $this->generateCalendarDays();
        }

        foreach ($this->calendarDays as &$day) {
            $day['hasConsultation'] = collect($this->consultationEvents)->contains('consultation_date', $day['date']);
        }
        unset($day);
    }

    public function changeMonth(int $offset): void
    {
        if (!$this->calendarEnabled || empty($this->availableMonths)) {
            $this->errorMessage = __('consultation::consultation.Calendar is not properly configured to change months.');

            return;
        }

        $currentCarbon = null;

        foreach ($this->availableMonths as $index => $monthData) {
            if ($monthData['carbon']->year === $this->currentMonth->year && $monthData['carbon']->month === $this->currentMonth->month) {
                $currentCarbon = $monthData['carbon'];
                $newIndex = $index + $offset;

                if (isset($this->availableMonths[$newIndex])) {
                    $this->currentMonth = $this->availableMonths[$newIndex]['carbon']->copy();
                    $this->generateCalendarDays();
                    $this->loadConsultations();
                }

                return;
            }
        }

        if (!$currentCarbon && !empty($this->availableMonths)) {
            $this->currentMonth = $this->availableMonths[0]['carbon']->copy();
            $this->generateCalendarDays();
            $this->loadConsultations();
        }
    }

    public function removeConsultation(int $eventId): void
    {
        try {
            $useCase = App::make(DeleteSessionConsultationUseCase::class);
            $result = $useCase->execute($eventId);

            if ($result) {
                $this->successMessage = __('consultation::consultation.Consultation successfully deleted');
                $this->dispatch('consultationDeleted');
                $this->loadConsultations();
            } else {
                $this->errorMessage = __('consultation::consultation.Failed to delete consultation');
            }
        } catch (Exception $e) {
            $this->errorMessage = __('consultation::consultation.Failed to delete consultation') . ': ' . $e->getMessage();
        }
    }

    public function isFirstMonth(): bool
    {
        if (!$this->calendarEnabled || empty($this->availableMonths) || !$this->currentMonth) {
            return true;
        }

        return $this->currentMonth->year === $this->availableMonths[0]['carbon']->year &&
               $this->currentMonth->month === $this->availableMonths[0]['carbon']->month;
    }

    public function isLastMonth(): bool
    {
        if (!$this->calendarEnabled || empty($this->availableMonths) || !$this->currentMonth) {
            return true;
        }
        $lastMonthData = end($this->availableMonths);

        return $this->currentMonth->year === $lastMonthData['carbon']->year &&
               $this->currentMonth->month === $lastMonthData['carbon']->month;
    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.my-session-consultation-calendar-component');
    }
}
