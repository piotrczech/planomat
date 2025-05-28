<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use Carbon\Carbon;
use Livewire\Component;
use Modules\Consultation\Infrastructure\Models\ConsultationSession;
use Exception;
use Illuminate\Support\Facades\App;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetSessionConsultationsUseCase;
use Livewire\Attributes\On;
use Modules\Consultation\Application\UseCases\ScientificWorker\DeleteSessionConsultationUseCase;

class MySessionConsultationCalendarComponent extends Component
{
    public array $consultationEvents;

    public Carbon $currentMonth;

    public array $calendarDays = [];

    public array $timeSlots = [];

    public string $activeTab = 'upcoming';

    public bool $showAddConsultationModal = false;

    public ?string $selectedDate = null;

    public ?string $selectedStartTime = null;

    public ?string $selectedEndTime = null;

    public string $location = '';

    // Daty sesji egzaminacyjnej
    public Carbon $sessionStart;

    public Carbon $sessionEnd;

    public array $availableMonths = [];

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        // Ustawiamy daty sesji - w rzeczywistej aplikacji, te daty powinny być pobierane z bazy danych
        $this->sessionStart = Carbon::create(2025, 1, 14);
        $this->sessionEnd = Carbon::create(2025, 2, 16);

        // Generujemy listę dostępnych miesięcy
        $this->generateAvailableMonths();

        // Ustawiamy aktualny miesiąc na pierwszy miesiąc sesji
        $this->currentMonth = $this->sessionStart->copy()->startOfMonth();

        $this->generateCalendarDays();
        $this->generateTimeSlots();
        $this->loadConsultations();
    }

    public function generateAvailableMonths(): void
    {
        $this->availableMonths = [];
        $startMonth = $this->sessionStart->copy()->startOfMonth();
        $endMonth = $this->sessionEnd->copy()->startOfMonth();

        $currentMonth = $startMonth->copy();

        while ($currentMonth->lte($endMonth)) {
            $this->availableMonths[] = [
                'year' => $currentMonth->year,
                'month' => $currentMonth->month,
                'name' => $currentMonth->translatedFormat('F Y'),
                'carbon' => $currentMonth->copy(),
            ];

            $currentMonth->addMonth();
        }
    }

    public function generateCalendarDays(): void
    {
        $this->calendarDays = [];
        $startOfMonth = $this->currentMonth->copy()->startOfMonth();
        $endOfMonth = $this->currentMonth->copy()->endOfMonth();

        // Określ pierwszy dzień do wyświetlenia (może być z poprzedniego miesiąca)
        $startDay = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);

        // Określ ostatni dzień do wyświetlenia (może być z następnego miesiąca)
        $endDay = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $currentDay = $startDay->copy();

        while ($currentDay->lte($endDay)) {
            $dateString = $currentDay->toDateString();
            $isCurrentMonth = $currentDay->month === $this->currentMonth->month;
            $isToday = $currentDay->isToday();
            $hasConsultation = false; // Będzie aktualizowane po załadowaniu konsultacji

            // Sprawdź, czy dzień jest w zakresie sesji
            $isInSessionRange = $currentDay->between($this->sessionStart, $this->sessionEnd);

            $this->calendarDays[] = [
                'date' => $dateString,
                'day' => $currentDay->day,
                'dayOfWeek' => $currentDay->dayOfWeek,
                'isCurrentMonth' => $isCurrentMonth,
                'isToday' => $isToday,
                'isInSessionRange' => $isInSessionRange,
                'hasConsultation' => $hasConsultation,
            ];

            $currentDay->addDay();
        }
    }

    public function generateTimeSlots(): void
    {
        $this->timeSlots = [
            'morning' => [
                '9:00', '9:30', '10:00', '10:30', '11:00', '11:30',
            ],
            'afternoon' => [
                '12:00', '12:30', '13:00', '13:30',
            ],
        ];
    }

    #[On('consultationSaved')]
    public function loadConsultations(): void
    {
        $this->consultationEvents = App::make(GetSessionConsultationsUseCase::class)->execute();

        foreach ($this->calendarDays as &$day) {
            $day['hasConsultation'] = collect($this->consultationEvents)->contains('consultation_date', $day['date']);
        }
        $this->dispatch('$refresh');
    }

    public function changeMonth(int $offset): void
    {
        $newMonth = $this->currentMonth->copy()->addMonths($offset);

        // Sprawdź, czy nowy miesiąc jest w zakresie sesji
        $isValidMonth = false;

        foreach ($this->availableMonths as $month) {
            if ($month['year'] === $newMonth->year && $month['month'] === $newMonth->month) {
                $isValidMonth = true;

                break;
            }
        }

        if ($isValidMonth) {
            $this->currentMonth = $newMonth;
            $this->generateCalendarDays();
            $this->loadConsultations();
        }
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function removeConsultation(int $eventId): void
    {
        try {
            $useCase = App::make(DeleteSessionConsultationUseCase::class);
            $result = $useCase->execute($eventId);

            if ($result) {
                $this->loadConsultations();
                $this->successMessage = __('consultation::consultation.Consultation successfully deleted');
                $this->dispatch('consultationDeleted');
            } else {
                $this->errorMessage = __('consultation::consultation.Failed to delete consultation');
            }
        } catch (Exception $e) {
            $this->errorMessage = __('consultation::consultation.Failed to delete consultation');
        }

        $this->generateCalendarDays();
        $this->loadConsultations();
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
    }

    public function selectTimeSlot(string $time, string $type = 'start'): void
    {
        if ($type === 'start') {
            $this->selectedStartTime = $time;

            // Automatycznie ustaw czas końcowy na 30 minut później, jeśli nie jest jeszcze wybrany
            if (!$this->selectedEndTime) {
                $carbonTime = Carbon::createFromFormat('H:i', $time);
                $this->selectedEndTime = $carbonTime->addMinutes(30)->format('H:i');
            }
        } else {
            $this->selectedEndTime = $time;
        }
    }

    public function openAddConsultationModal(): void
    {
        $this->showAddConsultationModal = true;
        $this->selectedDate = null;
        $this->selectedStartTime = null;
        $this->selectedEndTime = null;
        $this->location = '';
    }

    public function closeAddConsultationModal(): void
    {
        $this->showAddConsultationModal = false;
    }

    public function saveConsultation(): void
    {
        // Walidacja
        if (!$this->selectedDate || !$this->selectedStartTime || !$this->selectedEndTime) {
            // W rzeczywistej aplikacji wyświetlilibyśmy komunikat o błędzie
            return;
        }

        // Sprawdź, czy data jest w zakresie sesji
        $consultationDate = Carbon::parse($this->selectedDate);

        if (!$consultationDate->between($this->sessionStart, $this->sessionEnd)) {
            // Data poza zakresem sesji
            return;
        }

        // W rzeczywistej aplikacji zapisalibyśmy konsultację w bazie danych
        // ConsultationSession::create([
        //     'scientific_worker_id' => auth()->id(),
        //     'semester_id' => 1, // Aktualny semestr
        //     'consultation_date' => $this->selectedDate,
        //     'start_time' => $this->selectedStartTime,
        //     'end_time' => $this->selectedEndTime,
        //     'location' => $this->location,
        // ]);

        // Na potrzeby przykładu dodajemy do kolekcji
        $newId = max(array_column($this->consultationEvents, 'id')) + 1;
        $this->consultationEvents[] = [
            'id' => $newId,
            'consultation_date' => $this->selectedDate,
            'start_time' => $this->selectedStartTime,
            'end_time' => $this->selectedEndTime,
            'location' => $this->location,
            'status' => 'upcoming',
        ];

        // Zamknij modal i odśwież kalendarz
        $this->closeAddConsultationModal();
        $this->generateCalendarDays();
        $this->loadConsultations();
    }

    public function isFirstMonth(): bool
    {
        return $this->currentMonth->year === $this->availableMonths[0]['year'] &&
               $this->currentMonth->month === $this->availableMonths[0]['month'];
    }

    public function isLastMonth(): bool
    {
        $lastMonth = end($this->availableMonths);

        return $this->currentMonth->year === $lastMonth['year'] &&
               $this->currentMonth->month === $lastMonth['month'];
    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.my-session-consultation-calendar-component');
    }
}
