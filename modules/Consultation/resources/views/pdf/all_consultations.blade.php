<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Raport Konsultacji</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
        }
        @page {
            margin: 25mm 15mm;
        }
        .header {
            position: fixed;
            top: -20mm;
            left: 0;
            right: 0;
            text-align: center;
        }
        .header .title {
            font-size: 14px;
            font-weight: bold;
        }
        .header .subtitle {
            font-size: 12px;
        }
        .footer {
            position: fixed;
            bottom: -20mm;
            left: 0;
            right: 0;
            height: 15mm;
            border-top: 1px solid #555;
            font-size: 8px;
            color: #555;
        }
        .footer .page-number:before {
            content: "Strona " counter(page);
        }
        .footer .left { float: left; }
        .footer .right { float: right; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5mm;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px 7px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #E0E0E0;
            font-weight: bold;
        }
        tr { page-break-inside: avoid; }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #777;
        }
        .worker-name {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Konsultacje pracowników Wydziału Matematyki</div>
        <div class="subtitle">
            Typ: {{ $consultationType === \Modules\Consultation\Domain\Enums\ConsultationType::Semester ? 'Konsultacje semestralne' : 'Konsultacje sesyjne' }}
        </div>
    </div>
    
    <div class="footer">
        <div class="left">Wygenerowano: {{ $reportDate }}</div>
        <div class="right"><span class="page-number"></span></div>
    </div>

    @if($scientificWorkers->isEmpty())
        <p class="no-data">Brak pracowników naukowych w systemie.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:30%;">Prowadzący</th>
                    <th style="width:50%;">Termin</th>
                    <th style="width:20%;">Miejsce</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scientificWorkers as $worker)
                    @php
                        $consultations = $consultationType === \Modules\Consultation\Domain\Enums\ConsultationType::Semester
                            ? $worker->semesterConsultations
                            : $worker->sessionConsultations;
                        $consultationCount = $consultations->count();
                        $rowspan = $consultationCount > 0 ? $consultationCount : 1;
                    @endphp
                    <tr>
                        <td rowspan="{{ $rowspan }}" class="worker-name">
                            {{ $worker->name }}
                        </td>

                        @if($consultationCount > 0)
                            @php $first = $consultations->first(); @endphp
                            <td>
                                @if($consultationType === \Modules\Consultation\Domain\Enums\ConsultationType::Semester)
                                    {{ $first->day->label() }}
                                    @if($first->week_type !== \App\Domain\Enums\WeekTypeEnum::ALL)
                                        {{ $first->week_type->shortLabel() }}
                                    @endif
                                    {{ $first->start_time->format('H:i') }} - {{ $first->end_time->format('H:i') }}
                                @else
                                    {{ $first->consultation_date->format('d.m.Y') }}, 
                                    {{ $first->start_time->format('H:i') }} - {{ $first->end_time->format('H:i') }}
                                @endif
                            </td>
                            <td>
                                {{ $first->location_building }}@if($first->location_room),&nbsp;{{ $first->location_room }}@endif
                            </td>
                        @else
                            <td colspan="2" class="no-data">Brak zgłoszonych terminów</td>
                        @endif
                    </tr>
                    @if($consultationCount > 1)
                        @foreach($consultations->slice(1) as $consultation)
                            <tr>
                                <td>
                                     @if($consultationType === \Modules\Consultation\Domain\Enums\ConsultationType::Semester)
                                        {{ $consultation->day->label() }}
                                        @if($consultation->week_type !== \App\Domain\Enums\WeekTypeEnum::ALL)
                                            {{ $consultation->week_type->shortLabel() }}
                                        @endif
                                        , {{ $consultation->start_time->format('H:i') }} - {{ $consultation->end_time->format('H:i') }}
                                    @else
                                        {{ $consultation->consultation_date->format('d.m.Y') }},
                                        {{ $consultation->start_time->format('H:i') }} - {{ $consultation->end_time->format('H:i') }}
                                    @endif
                                </td>
                                <td>
                                    {{ $consultation->location_building }}@if($consultation->location_room),&nbsp;{{ $consultation->location_room }}@endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html> 