@php
    use App\Enums\CoursePreferenceTypeEnum;

    /** @var \Modules\Desiderata\Infrastructure\Models\Desideratum $desideratum */
    $worker = $desideratum->scientificWorker;
@endphp

<div class="page-content">
    <div class="employee-header">
        Preferencje dydaktyczne: {{ $worker->name }}
    </div>

    @if(!$desideratum->exists)
        <div class="no-submission-notice">
            Pracownik nie złożył dezyderaty w tym semestrze.
        </div>
    @endif

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
                @if($couldCourses->isNotEmpty())
                    <ul style="margin: 0; padding: 0; list-style-type: none;">
                        @foreach($couldCourses as $pref)
                            <li style="margin-bottom: 0.5em;">{{ $pref->course?->name ?? 'Błąd' }}</li>
                        @endforeach
                    </ul>
                @else
                    Brak wskazanych.
                @endif
            </td>
        </tr>
        <tr>
            <td>Kursy, które <strong>chciałbym</strong> prowadzić:</td>
            <td style="width: 60%;">
                @if($wantedCourses->isNotEmpty())
                    <ul style="margin: 0; padding: 0; list-style-type: none;">
                        @foreach($wantedCourses as $pref)
                            <li style="margin-bottom: 0.5em;">{{ $pref->course?->name ?? 'Błąd' }}</li>
                        @endforeach
                    </ul>
                @else
                    Brak wskazanych.
                @endif
            </td>
        </tr>
        <tr>
            <td>Kursy, których <strong>nie chciałbym</strong> prowadzić:</td>
            <td style="width: 60%;">
                @if($unwantedCourses->isNotEmpty())
                    <ul style="margin: 0; padding: 0; list-style-type: none;">
                         @foreach($unwantedCourses as $pref)
                            <li style="margin-bottom: 0.5em;">{{ $pref->course?->name ?? 'Błąd' }}</li>
                        @endforeach
                    </ul>
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
            <td>Maksymalnie godzin pod rząd:</td>
            <td style="width: 60%;">{{ $desideratum->max_consecutive_hours ?? '-' }}</td>
        </tr>
    </table>

    @if($desideratum->additional_notes)
        <div class="section-title">Dodatkowe uwagi</div>
        <div class="notes-box">
            {!! nl2br(e($desideratum->additional_notes)) !!}
        </div>
    @endif
</div> 