@php
    /** @var \Modules\Desiderata\Infrastructure\Models\Desideratum $desideratum */
    /** @var \App\Models\User $worker */
    $worker = $desideratum->scientificWorker;
    $semester = $desideratum->semester;

    // Przygotowanie danych o niedostępności w bardziej użytecznej strukturze
    // Kluczem będzie np. 'monday_1' (dzień_idslotu)
    $unavailableSlotsMap = [];
    foreach ($desideratum->unavailableTimeSlots as $unavailableSlot) {
        // Upewniamy się, że używamy wartości string enuma, jeśli $unavailableSlot->day jest obiektem Enum
        $dayValue = is_string($unavailableSlot->day) ? $unavailableSlot->day : $unavailableSlot->day->value;
        $unavailableSlotsMap[$dayValue . '_' . $unavailableSlot->time_slot_id] = true;
    }

    // Definicja dni tygodnia i slotów czasowych - zgodnie ze specyfikacją
    // W idealnym świecie te dane pochodziłyby z bazy lub konfiguracji,
    // ale dla spójności z @desiderata-form-availability-step-component.blade.php
    // i dla uproszczenia PDF, możemy je tu zdefiniować.
    // Zakładamy, że mamy dostęp do WeekdayEnum i TimeSlotModel (lub jego odpowiednika dla PDF)

    // Jeśli nie mamy łatwego dostępu do wszystkich TimeSlotów z bazy przez repozytorium dla każdego pracownika,
    // możemy przyjąć stałą listę slotów czasowych zgodną ze specyfikacją.
    // Załóżmy, że TimeSlot::all() lub podobna metoda da nam wszystkie możliwe sloty.
    // Dla celów tego przykładu, zahardkoduję sloty, ale w rzeczywistej aplikacji
    // powinno to być dynamiczne lub oparte na tych samych danych co formularz.

    // Spróbujmy pobrać wszystkie możliwe sloty czasowe - to powinno być zrobione
    // raz w UseCase i przekazane do widoku, jeśli sloty są dynamiczne.
    // Tutaj zakładam, że $allTimeSlots jest przekazywane lub dostępne globalnie (co nie jest idealne).
    // Dla bezpieczeństwa, zdefiniujmy je statycznie, jeśli nie są dostępne.
    if (!isset($allTimeSlots)) {
        // Ta lista powinna odpowiadać temu co jest w bazie i w komponencie Livewire
        // `id` musi pasować do `time_slot_id` z `desideratum_unavailable_time_slots`
        $allTimeSlots = collect([
            (object)['id' => 1, 'range' => '07:30 - 09:00'],
            (object)['id' => 2, 'range' => '09:15 - 11:00'],
            (object)['id' => 3, 'range' => '11:15 - 13:00'],
            (object)['id' => 4, 'range' => '13:15 - 15:00'],
            (object)['id' => 5, 'range' => '15:15 - 16:55'],
            (object)['id' => 6, 'range' => '17:05 - 18:45'],
            (object)['id' => 7, 'range' => '18:55 - 20:35'],
        ]);
    }
    $weekdays = \App\Enums\WeekdayEnum::cases();

@endphp

<div class="employee-header">
    Dostępność czasowa: {{ $worker->title ?? '' }} {{ $worker->name ?? '' }} {{ $worker->surname ?? 'N/A' }}<br>
    <small>Semestr: {{ $semester->name ?? 'N/A' }} ({{ $semester->academic_year ?? 'N/A' }})</small>
</div>

<div class="section-title">Harmonogram niedostępności</div>
<p style="font-size: 9px; color: #555; margin-bottom: 10px;">Poniższa tabela przedstawia zadeklarowane przez pracownika bloki czasowe, w których <strong>NIE JEST</strong> on dostępny. Puste pola oznaczają dostępność.</p>

<table style="font-size: 9px;">
    <thead>
        <tr>
            <th style="width: 15%; background-color: #f0f0f0; text-align:center;">Godzina</th>
            @foreach ($weekdays as $dayEnum)
                <th style="background-color: #f0f0f0; text-align:center;">{{ $dayEnum->label() }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($allTimeSlots as $slot)
            <tr>
                <td style="text-align:center; font-weight:bold;">{{ $slot->range }}</td>
                @foreach ($weekdays as $dayEnum)
                    @php
                        $isUnavailable = isset($unavailableSlotsMap[$dayEnum->value . '_' . $slot->id]);
                    @endphp
                    <td style="text-align:center; height: 30px; border: 1px solid #ccc; @if($isUnavailable) background-color: #e0e0e0; @endif">
                        @if($isUnavailable)
                           X
                        @else
                           &nbsp; {{-- Puste pole dla dostępnych --}}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
<p style="font-size: 8px; margin-top: 5px;">Legenda: <span style="font-weight: bold;">X</span> - Niedostępny &nbsp;&nbsp; <span style="display:inline-block; width:10px; height:10px; background-color:#fff; border:1px solid #ccc;"></span> - Dostępny</p>

@if(empty($desideratum->unavailableTimeSlots) || $desideratum->unavailableTimeSlots->isEmpty())
    <p style="text-align:center; font-style:italic; margin-top:20px;">Pracownik nie zadeklarował żadnych niedostępnych bloków czasowych (pełna dostępność w ramach siatki).</p>
@else
    <p style="text-align:center; font-size: 9px; margin-top:10px;">Liczba zadeklarowanych niedostępnych bloków: {{ $desideratum->unavailableTimeSlots->count() }} z 5 dozwolonych.</p>
@endif 