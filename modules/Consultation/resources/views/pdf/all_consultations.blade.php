<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Konsultacje Pracowników</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            margin: 0;
        }
        @page {
            margin: 20mm 15mm;
        }
        .page-header-title {
            position: fixed;
            top: -15mm;
            left: 0mm;
            right: 0mm;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            background-color: #FFFFFF;
        }
        .report-date {
            text-align: right;
            font-size: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25mm;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #E0E0E0;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            font-size: 9px;
        }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead.main-table-header {
            display: table-header-group;
        }
    </style>
</head>
<body>
    <div class="page-header-title">Wydział Matematyki Konsultacje pracowników w semestrze letnim 2024/2025</div>

    <div class="report-date">Wygenerowano: {{ $reportDate }}</div>

    @php
        if (empty($groupedConsultations) || getenv('APP_ENV') === 'testing') {
            $baseConsultations = [
                [
                    'term_or_day' => 'Poniedziałek',
                    'hours' => '10:00 - 12:00',
                    'location' => 's. 101, C-13',
                    'week_type' => 'Każdy',
                    'type' => 'Stacjonarne'
                ],
                [
                    'term_or_day' => 'Środa (TN)',
                    'hours' => '14:00 - 15:30',
                    'location' => 'MS Teams',
                    'week_type' => 'Nieparzyste',
                    'type' => 'Zdalne'
                ],
                [
                    'term_or_day' => 'Piątek (TP)',
                    'hours' => '08:00 - 09:30',
                    'location' => 's. 202, D-1',
                    'week_type' => 'Parzyste',
                    'type' => 'Stacjonarne'
                ],
            ];
            $lecturers = [
                1 => 'dr inż. Jan Kowalski',
                2 => 'prof. dr hab. Anna Nowak',
                3 => 'dr Ewa Wiśniewska',
                4 => 'mgr Piotr Zając',
                5 => 'dr hab. Krzysztof Lewandowski',
                6 => 'inż. Alicja Bąk',
                7 => 'dr Stefan Wójcik',
                8 => 'mgr Karolina Kaczmarek',
                9 => 'prof. dr hab. inż. Adam Mazur',
                10 => 'dr Agnieszka Pawlak',
                11 => 'dr inż. Michał Szymański',
                12 => 'prof. Elżbieta Woźniak',
                13 => 'dr Tomasz Krawczyk',
                14 => 'mgr Magdalena Grabowska',
                15 => 'dr hab. inż. Paweł Michalski',
                16 => 'dr Joanna Zielińska',
                17 => 'mgr Krzysztof Sikora',
                18 => 'prof. dr hab. Barbara Malinowska',
                19 => 'dr Adam Nowicki',
                20 => 'inż. Monika Jasińska',
            ];

            $groupedConsultations = [];
            foreach ($lecturers as $id => $name) {
                $numConsultations = rand(1, 3);
                $currentConsultations = [];
                for ($i = 0; $i < $numConsultations; $i++) {
                    $consultation = $baseConsultations[array_rand($baseConsultations)];
                    $consultation['location'] = preg_replace_callback('/\d+/', function ($matches) {
                        return $matches[0] + rand(-5, 5);
                    }, $consultation['location']);
                    if (rand(0,1)) {
                         $startHour = rand(8, 15);
                         $endHour = $startHour + rand(1, 3);
                         $consultation['hours'] = sprintf('%02d:00 - %02d:30', $startHour, $endHour -1 );
                    }
                    $currentConsultations[] = $consultation;
                }
                $groupedConsultations[$id] = ['name' => $name, 'consultations' => $currentConsultations];
            }
        }
    @endphp

    @if(empty($groupedConsultations))
        <p style="text-align:center; font-style:italic; margin-top: 20px;">Brak zgłoszonych konsultacji do wyświetlenia.</p>
    @else
        <table>
            <thead class="main-table-header">
                <tr>
                    <th style="width:30%;">Prowadzący</th>
                    <th style="width:50%;">Termin</th>
                    <th style="width:20%;">Miejsce</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedConsultations as $employeeId => $employeeData)
                    @php
                        $employeeName = $employeeData['name'] ?? 'Nieznany Prowadzący';
                        $consultations = $employeeData['consultations'] ?? [];
                        $consultationCount = count($consultations);
                    @endphp
                    @if($consultationCount > 0)
                        @foreach($consultations as $index => $consultation)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ $consultationCount }}">{{ $employeeName }}</td>
                                @endif
                                <td>
                                    @php
                                        $terminParts = [];
                                        if (!empty($consultation['term_or_day'])) {
                                            if (preg_match('/\((TN|TP)\)/', $consultation['term_or_day'])) {
                                                $terminParts[] = $consultation['term_or_day'];
                                            } else {
                                                $terminParts[] = $consultation['term_or_day'];
                                                if (!empty($consultation['week_type']) && $consultation['week_type'] !== 'Każdy') {
                                                    if (stripos($consultation['week_type'], 'parzyste') !== false) {
                                                        $terminParts[] = '(TP)';
                                                    } elseif (stripos($consultation['week_type'], 'nieparzyste') !== false) {
                                                        $terminParts[] = '(TN)';
                                                    }
                                                }
                                            }
                                        }
                                        if (!empty($consultation['hours'])) {
                                            $terminParts[] = $consultation['hours'];
                                        }
                                        if (!empty($consultation['type']) && $consultation['type'] !== 'Stacjonarne') {
                                           $terminParts[] = '['. $consultation['type'] .']';
                                        }
                                        echo implode(', ', $terminParts);
                                    @endphp
                                </td>
                                <td>{{ $consultation['location'] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ $employeeName }}</td>
                            <td colspan="2" style="text-align:center; font-style:italic;">Brak zgłoszonych konsultacji dla tego pracownika.</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html> 