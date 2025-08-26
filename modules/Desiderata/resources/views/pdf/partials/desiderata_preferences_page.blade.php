@php
    use App\Domain\Enums\CoursePreferenceTypeEnum;

    /** @var \Modules\Desiderata\Infrastructure\Models\Desideratum $desideratum */
    $worker = $desideratum->scientificWorker;
@endphp

<div class="page-content">
    <div class="employee-header">
        {{ $worker->name }}

        @if(!$desideratum->exists)
            (pracownik nie uzupełnił dokumentu)
        @endif
    </div>

    <div class="section-title">Preferencje ogólne</div>
    <table>
        <tr>
            <th style="width: 40%;">Kategoria</th>
            <th style="width: 60%;">Odpowiedź</th>
        </tr>
        <tr>
            <td>Forma prowadzenia zajęć:</td>
            <td>
                @if($desideratum->want_stationary) Stacjonarne @endif
                @if($desideratum->want_stationary && $desideratum->want_non_stationary) / @endif
                @if($desideratum->want_non_stationary) Niestacjonarne @endif
                @if(!$desideratum->exists) <span style="color: #777;">-</span> @endif
            </td>
        </tr>
        <tr>
            <td>Zgoda na nadgodziny:</td>
            <td>{{ $desideratum->exists ? ($desideratum->agree_to_overtime ? 'Tak' : 'Nie') : '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Preferencje kursów</div>
    <table>
        @php
            $coursePreferences = $desideratum->coursePreferences ?? collect();
            $wantedCourses = $coursePreferences->where('type', CoursePreferenceTypeEnum::WANTED);
            $couldCourses = $coursePreferences->where('type', CoursePreferenceTypeEnum::COULD);
            $unwantedCourses = $coursePreferences->where('type', CoursePreferenceTypeEnum::UNWANTED);
        @endphp
        <tr>
            <td style="width: 40%;">Kursy, które <strong>mogę</strong> prowadzić:</td>
            <td style="width: 60%;">
                @if(!$desideratum->exists)
                    {{ $allCourses->map(fn($course) => $course->name)->join(', ') }}
                @elseif($couldCourses->isNotEmpty())
                    {{ $couldCourses->map(fn($pref) => $pref->course?->name ?? 'Błąd')->join(', ') }}
                @else
                    Brak wskazanych.
                @endif
            </td>
        </tr>
        <tr>
            <td>Kursy, które <strong>chciałbym</strong> prowadzić:</td>
            <td style="width: 60%;">
                @if(!$desideratum->exists)

                @elseif($wantedCourses->isNotEmpty())
                    {{ $wantedCourses->map(fn($pref) => $pref->course?->name ?? 'Błąd')->join(', ') }}
                @else
                    Brak wskazanych.
                @endif
            </td>
        </tr>
        <tr>
            <td>Kursy, których <strong>nie chciałbym</strong> prowadzić:</td>
            <td style="width: 60%;">
                @if(!$desideratum->exists)

                @elseif($unwantedCourses->isNotEmpty())
                    {{ $unwantedCourses->map(fn($pref) => $pref->course?->name ?? 'Błąd')->join(', ') }}
                @else
                    Brak wskazanych.
                @endif
            </td>
        </tr>
    </table>

    <div class="section-title">Prowadzone prace dyplomowe</div>
    <table>
        <tr>
            <td style="width: 40%;">Prace magisterskie:</td>
            <td style="width: 60%;">{{ $desideratum->master_theses_count ?? '-' }}</td>
        </tr>
        <tr>
            <td>Prace inżynierskie/licencjackie:</td>
            <td style="width: 60%;">{{ $desideratum->bachelor_theses_count ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Preferencje godzinowe</div>
    <table>
        <tr>
            <td style="width: 40%;">Maksymalnie godzin dziennie:</td>
            <td style="width: 60%;">{{ $desideratum->max_hours_per_day ?? '-' }}</td>
        </tr>
        <tr>
            <td>Maksymalnie godzin z rzędu:</td>
            <td style="width: 60%;">{{ $desideratum->max_consecutive_hours ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Dodatkowe uwagi</div>
    <div class="notes-box" style="max-height: 100px; overflow: hidden;">
        @if($desideratum->additional_notes)
            {!! nl2br(str_replace(["\r", "\n"], ' ', e($desideratum->additional_notes))) !!}
        @else
            <span style="color: #777;">brak</span>
        @endif
    </div>
</div> 