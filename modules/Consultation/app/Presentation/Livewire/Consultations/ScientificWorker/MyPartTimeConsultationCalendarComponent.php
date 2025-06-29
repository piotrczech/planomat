<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use App\Application\UseCases\Semester\GetCurrentSemesterDatesUseCase;
use Carbon\Carbon;
use Livewire\Component;
use Exception;
use Illuminate\Support\Facades\App;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetPartTimeConsultationsUseCase;
use Livewire\Attributes\On;
use Modules\Consultation\Application\UseCases\ScientificWorker\DeletePartTimeConsultationUseCase;

class MyPartTimeConsultationCalendarComponent extends Component
{
    public array $consultationEvents = [];

    public Carbon $currentMonth;

    public array $calendarDays = [];

    public ?Carbon $semesterStart = null;

    public ?Carbon $sessionStartPeriodEnd = null;

    public array $availableMonths = [];

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public bool $calendarEnabled = false;

    public function mount(): void
    {
        $semesterDatesUseCase = App::make(GetCurrentSemesterDatesUseCase::class);
        $dates = $semesterDatesUseCase->execute();

        if ($dates && isset($dates['semester_start_date'], $dates['session_start_date'])) {
            $this->semesterStart = Carbon::parse($dates['semester_start_date']);
            $this->sessionStartPeriodEnd = Carbon::parse($dates['session_start_date']);
            $this->calendarEnabled = true;
        } else {
            $this->errorMessage = __('consultation::consultation.No current semester dates found. Calendar functionality may be limited.');
            $this->semesterStart = Carbon::now()->startOfMonth();
            $this->sessionStartPeriodEnd = Carbon::now()->endOfMonth();
            $this->calendarEnabled = false;
        }

        if ($this->calendarEnabled) {
            $this->generateAvailableMonths();
            $this->currentMonth = $this->semesterStart->copy()->startOfMonth();
        } else {
            $this->currentMonth = Carbon::now()->startOfMonth();
        }

        $this->generateCalendarDays();
        $this->loadConsultations();
    }

    public function generateAvailableMonths(): void
    {
        $this->availableMonths = [];

        if (!$this->semesterStart || !$this->sessionStartPeriodEnd) {
            return;
        }

        $startMonth = $this->semesterStart->copy()->startOfMonth();
        $endMonth = $this->sessionStartPeriodEnd->copy()->startOfMonth();

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

        if (!$this->semesterStart || !$this->sessionStartPeriodEnd) {
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
            $isInRange = $this->calendarEnabled && $this->semesterStart && $this->sessionStartPeriodEnd && $currentDayIterator->between($this->semesterStart, $this->sessionStartPeriodEnd);

            $this->calendarDays[] = [
                'date' => $dateString,
                'day' => $currentDayIterator->day,
                'dayOfWeek' => $currentDayIterator->dayOfWeek,
                'isCurrentMonth' => $isCurrentMonth,
                'isToday' => $isToday,
                'isInRange' => $isInRange && $currentDayIterator->isWeekend(),
            ];

            $currentDayIterator->addDay();
        }
    }

    #[On('consultationSaved')]
    public function loadConsultations(): void
    {
        $this->consultationEvents = App::make(GetPartTimeConsultationsUseCase::class)->execute();

        if (empty($this->calendarDays) && ($this->semesterStart && $this->sessionStartPeriodEnd)) {
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

        foreach ($this->availableMonths as $index => $monthData) {
            if ($monthData['carbon']->year === $this->currentMonth->year && $monthData['carbon']->month === $this->currentMonth->month) {
                $newIndex = $index + $offset;

                if (isset($this->availableMonths[$newIndex])) {
                    $this->currentMonth = $this->availableMonths[$newIndex]['carbon']->copy();
                    $this->generateCalendarDays();
                    $this->loadConsultations();
                }

                return;
            }
        }
    }

    public function removeConsultation(int $eventId): void
    {
        try {
            $useCase = App::make(DeletePartTimeConsultationUseCase::class);
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
        return view('consultation::components.consultations.scientific-worker.my-part-time-consultation-calendar-component');
    }
}
