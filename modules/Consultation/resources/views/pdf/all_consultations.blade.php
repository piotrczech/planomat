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
    <div class="report-date">Wygenerowano: {{ $reportDate }}</div>

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