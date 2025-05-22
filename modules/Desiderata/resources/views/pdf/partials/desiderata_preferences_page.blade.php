@php
    /** @var \Modules\Desiderata\Infrastructure\Models\Desideratum $desideratum */
    $worker = $desideratum->scientificWorker;
    $semester = $desideratum->semester; // Choć nie jest bezpośrednio w specyfikacji PDF, może się przydać
@endphp

<div class="employee-header">
    Dezyderat: {{ $worker->title ?? '' }} {{ $worker->name ?? '' }} {{ $worker->surname ?? 'N/A' }}<br>
    <small>Semestr: {{ $semester->name ?? 'N/A' }} ({{ $semester->academic_year ?? 'N/A' }})</small><br>
    <small style="font-size: 8px; color: #888;">Data ostatniej modyfikacji dezyderatu: {{ $desideratum->updated_at ? $desideratum->updated_at->translatedFormat('d F Y H:i') : 'Brak danych' }}</small>
</div>

<div class="section-title">Preferencje dydaktyczne</div>

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
            @if(!$desideratum->want_stationary && !$desideratum->want_non_stationary) <span style="color: red;">Nie wybrano</span> @endif
        </td>
    </tr>
    <tr>
        <td>Zgoda na nadgodziny:</td>
        <td>{{ $desideratum->agree_to_overtime ? 'Tak' : 'Nie' }}</td>
    </tr>
</table>

<div class="section-title" style="margin-top: 5px;">Preferencje kursów</div>
<table>
    <tr>
        <td style="width: 40%;">Kursy, które mogę prowadzić:</td>
        <td style="width: 60%;">
            @if($desideratum->couldCourses->isNotEmpty())
                <ul>
                    @foreach($desideratum->couldCourses as $course)
                        <li>{{ $course->name }}</li>
                    @endforeach
                </ul>
            @else
                Brak wskazanych.
            @endif
        </td>
    </tr>
    <tr>
        <td>Kursy, które chciałbym prowadzić:</td>
        <td>
            @if($desideratum->wantedCourses->isNotEmpty())
                <ul>
                    @foreach($desideratum->wantedCourses as $course)
                        <li>{{ $course->name }}</li>
                    @endforeach
                </ul>
            @else
                Brak wskazanych.
            @endif
        </td>
    </tr>
    <tr>
        <td>Kursy, których nie chciałbym prowadzić (max 2):</td>
        <td>
            @if($desideratum->notWantedCourses->isNotEmpty())
                <ul>
                    @foreach($desideratum->notWantedCourses as $course)
                        <li>{{ $course->name }}</li>
                    @endforeach
                </ul>
            @else
                Brak wskazanych.
            @endif
        </td>
    </tr>
</table>

<div class="section-title" style="margin-top: 5px;">Prowadzone prace dyplomowe</div>
<table>
    <tr>
        <td style="width: 40%;">Prace magisterskie:</td>
        <td style="width: 60%;">{{ $desideratum->master_theses_count }}</td>
    </tr>
    <tr>
        <td>Prace inżynierskie/licencjackie:</td>
        <td>{{ $desideratum->bachelor_theses_count }}</td>
    </tr>
</table>

<div class="section-title" style="margin-top: 5px;">Preferencje godzinowe</div>
<table>
    <tr>
        <td style="width: 40%;">Maksymalnie godzin dziennie:</td>
        <td style="width: 60%;">{{ $desideratum->max_hours_per_day }}</td>
    </tr>
    <tr>
        <td>Maksymalnie godzin pod rząd:</td>
        <td>{{ $desideratum->max_consecutive_hours }}</td>
    </tr>
</table>

@if($desideratum->additional_notes)
    <div class="section-title" style="margin-top: 5px;">Dodatkowe uwagi</div>
    <div style="border: 1px solid #eee; padding: 8px; background-color: #fdfdfd;">
        {!! nl2br(e($desideratum->additional_notes)) !!}
    </div>
@endif 