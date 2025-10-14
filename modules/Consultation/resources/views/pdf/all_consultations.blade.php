@php
    use Modules\Consultation\Domain\Enums\ConsultationType;
    use App\Domain\Enums\SemesterSeasonEnum;

    if (isset($semester)) {
        if ($consultationType === ConsultationType::Session) {
            $seasonLabel = $semester->season === SemesterSeasonEnum::WINTER
                ? __('consultation::consultation.in_session_winter')
                : __('consultation::consultation.in_session_summer');
        } else {
            $seasonLabel = $semester->season === SemesterSeasonEnum::WINTER
                ? __('consultation::consultation.in_semester_winter')
                : __('consultation::consultation.in_semester_summer');
        }
    } else {
        $seasonLabel = 'Brak semestru';
    }

    $color = '#ffffff'
@endphp

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
            page-break-before: always;
        }
        @page {
            margin: 10mm 15mm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #cccccc;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000000;
            padding: 4px;
        }

        td {
            border: 1px solid #000000;
            padding: 2px 4px;
        }

        footer {
            margin-top: 30px;
            color: #888;
            font-size: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    @if(empty($processedWorkers))
        <p class="no-data">Brak konsultacji do wyświetlenia.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th colspan="3">
                        Wydział Matematyki Konsultacje pracowników w semestrze {{ $seasonLabel }} {{ $semester->academic_year }}
                    </th>
                </tr>
                <tr>
                    <th style="width:30%;">Prowadzący</th>
                    <th style="width:50%;">Termin</th>
                    <th style="width:20%;">Miejsce</th>
                </tr>
            </thead>
            <tbody>
                @foreach($processedWorkers as $workerData)
                    @if(count($workerData['lines']) > 0)
                        @foreach($workerData['lines'] as $line)
                            <tr style="background-color: {{ $color }};">
                                <td style="
                                    background-color: {{ $color }};
                                    @if(!$loop->first)
                                        border-top: 0px solid #000000 !important;
                                    @endif
                                    @if(count($workerData['lines']) > 1)
                                        border-bottom: 0px solid #000000 !important;
                                    @endif
                                ">
                                    {{ $loop->first ? $workerData['name'] : '' }}
                                </td>

                                <td style="background-color: {{ $color }};">
                                    {{ $line['schedule'] }}
                                </td>
                                <td style="background-color: {{ $color }};">
                                    {!! $line['location'] !!}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td style="font-weight: bold;">{{ $workerData['name'] }}</td>
                            <td colspan="2" class="no-data">Brak zgłoszonych terminów</td>
                        </tr>
                    @endif
                    @php
                        $color = $color === '#ffffff' ? '#f3f3f3' : '#ffffff';
                    @endphp
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <div class="left">Wygenerowano: {{ $reportDate }}</div>
        <div class="right"><span class="page-number"></span></div>
    </div>
</body>
</html> 