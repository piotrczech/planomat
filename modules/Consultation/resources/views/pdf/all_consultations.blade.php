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
        @php
            use Modules\Consultation\Domain\Enums\ConsultationType;
            use App\Domain\Enums\SemesterSeasonEnum;

            $titleText = 'Konsultacje';

            if (isset($semester)) {
                if ($consultationType === ConsultationType::Session) {
                    $seasonLabel = $semester->season === SemesterSeasonEnum::WINTER
                        ? __('consultation::consultation.in_session_winter')
                        : __('consultation::consultation.in_session_summer');

                    $titleText = 'Konsultacje w sesji ' . $seasonLabel . ' ' . $semester->academic_year;
                } else {
                    $seasonLabel = $semester->season === SemesterSeasonEnum::WINTER
                        ? __('consultation::consultation.in_semester_winter')
                        : __('consultation::consultation.in_semester_summer');

                    $titleText = 'Konsultacje w semestrze ' . $seasonLabel . ' ' . $semester->academic_year;
                }
            }
        @endphp

        <div class="title">{{ $titleText }}</div>
    </div>
    
    <div class="footer">
        <div class="left">Wygenerowano: {{ $reportDate }}</div>
        <div class="right"><span class="page-number"></span></div>
    </div>

    @if(empty($processedWorkers))
        <p class="no-data">Brak konsultacji do wyświetlenia.</p>
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
                @foreach($processedWorkers as $workerData)
                    @php
                        $rowspan = count($workerData['lines']) > 0 ? count($workerData['lines']) : 1;
                    @endphp
                    <tr>
                        <td rowspan="{{ $rowspan }}" class="worker-name">
                            {{ $workerData['name'] }}
                        </td>

                        @if(count($workerData['lines']) > 0)
                            @php $isFirstRow = true; @endphp
                            @foreach($workerData['lines'] as $line)
                                @if (!$isFirstRow)
                                    <tr>
                                @endif
                                
                                <td>
                                    @if($line['is_html'])
                                        {!! $line['schedule'] !!}
                                    @else
                                        {{ $line['schedule'] }}
                                    @endif
                                </td>
                                <td>{!! $line['location'] !!}</td>
                                
                                @if ($isFirstRow)
                                    @php $isFirstRow = false; @endphp
                                @else
                                    </tr>
                                @endif
                            @endforeach
                            {{-- This condition ensures that the very first <tr> is closed if there were any lines --}}
                            @if(!$isFirstRow)
                                </tr> 
                            @endif
                        @else
                            <td colspan="2" class="no-data">Brak zgłoszonych terminów</td>
                            </tr>
                        @endif
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html> 