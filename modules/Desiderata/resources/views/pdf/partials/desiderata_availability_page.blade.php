@php
    /** @var \Modules\Desiderata\Infrastructure\Models\Desideratum $desideratum */
    /** @var \App\Infrastructure\Models\User $worker */
    $worker = $desideratum->scientificWorker;

    $unavailableSlotsMap = [];
    if ($desideratum->exists) {
        foreach ($desideratum->unavailableTimeSlots as $unavailableSlot) {
            $dayValue = is_string($unavailableSlot->day) ? $unavailableSlot->day : $unavailableSlot->day->value;
            $unavailableSlotsMap[$dayValue . '_' . $unavailableSlot->time_slot_id] = true;
        }
    }

    if (!isset($allTimeSlots)) {
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
    $weekdays = \App\Domain\Enums\WeekdayEnum::cases();
@endphp

<div class="page-content">
    @if(!$desideratum->exists)
        <div class="no-submission-notice">
            Pracownik nie złożył dezyderaty w tym semestrze.
        </div>
    @endif

    <table style="font-size: 9px;">
        <small>{{ $worker->name }}:</small>
        <thead>
        <tr>
            <th style="width: 10%; background-color: #f0f0f0; text-align:center;">Godzina</th>
            @foreach ($weekdays as $dayEnum)
                <th style="width: 15%; background-color: #f0f0f0; text-align:center;">{{ $dayEnum->label() }}</th>
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
                    <td style="text-align:left; font-weight: bold; font-size: 20px; vertical-align: top; height: 70px; border: 1px solid #ccc; @if($isUnavailable) background-color: #e0e0e0; @endif">
                        @if($isUnavailable)
                            X
                        @else
                            &nbsp;
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div> 