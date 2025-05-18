<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use Carbon\Carbon;
use Livewire\Component;
use Modules\Consultation\Infrastructure\Models\ConsultationSession;
use Exception;

class MySessionConsultationCalendarComponent extends Component
{
    public array $consultationEvents = [];

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

    public function loadConsultations(): void
    {
        // W rzeczywistym kodzie, pobieralibyśmy dane z bazy
        // $consultations = ConsultationSession::where('scientific_worker_id', auth()->id())
        //    ->whereBetween('consultation_date', [$this->sessionStart->toDateString(), $this->sessionEnd->toDateString()])
        //    ->orderBy('consultation_date')
        //    ->orderBy('start_time')
        //    ->get();

        // Na potrzeby przykładu używamy tych samych danych co wcześniej
        $consultations = collect([
            [
                'id' => 1,
                'consultation_date' => '2025-01-13',
                'start_time' => '09:05',
                'end_time' => '10:30',
                'location' => 'Sala 204, Budynek Główny',
                'status' => 'upcoming',
            ],
            [
                'id' => 2,
                'consultation_date' => '2025-01-15',
                'start_time' => '13:15',
                'end_time' => '14:45',
                'location' => 'Sala konferencyjna',
                'status' => 'upcoming',
            ],
            [
                'id' => 3,
                'consultation_date' => '2025-02-10',
                'start_time' => '16:00',
                'end_time' => '17:30',
                'location' => 'Gabinet 312',
                'status' => 'upcoming',
            ],
        ]);

        $this->consultationEvents = $consultations->toArray();

        // Oznacz dni z konsultacjami w kalendarzu
        foreach ($this->calendarDays as &$day) {
            $day['hasConsultation'] = $consultations->contains('consultation_date', $day['date']);
        }
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
            // W rzeczywistej aplikacji usunęlibyśmy obiekt z bazy danych
            // ConsultationSession::find($eventId)->delete();

            $this->consultationEvents = array_filter(
                $this->consultationEvents,
                fn ($event) => $event['id'] !== $eventId,
            );

            $this->successMessage = __('consultation::consultation.Consultation successfully deleted');
        } catch (Exception $e) {
            $this->errorMessage = __('consultation::consultation.Failed to delete consultation');
        }

        // Aktualizacja dni kalendarza
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
