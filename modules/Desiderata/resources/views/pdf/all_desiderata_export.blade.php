<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Dezyderaty Pracowników - {{ $reportDate }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            color: #333;
        }
        @page {
            margin: 20mm 15mm;
        }
        .page-break {
            page-break-after: always;
        }
        .employee-header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 10px;
            color: #555;
        }
        .footer-info {
            position: fixed;
            bottom: -15mm;
            left: 0mm;
            right: 0mm;
            text-align: center;
            font-size: 8px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .report-generated-date {
            position: fixed;
            top: -15mm;
            right: 0mm;
            text-align: right;
            font-size: 8px;
            color: #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        ul {
            list-style-type: disc;
            padding-left: 20px;
        }
        li {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="report-generated-date">Wygenerowano: {{ $reportDate }}</div>

    @if($allDesiderata->isEmpty())
        <p style="text-align:center; font-style:italic; margin-top: 50px;">Brak dezyderatów do wyświetlenia.</p>
    @else
        @foreach($allDesiderata as $desideratum)
            {{-- Page 1: Desideratum Preferences --}}
            @include('desiderata::pdf.partials.desiderata_preferences_page', ['desideratum' => $desideratum])
            <div class="page-break"></div>

            {{-- Page 2: Desideratum Availability --}}
            @include('desiderata::pdf.partials.desiderata_availability_page', ['desideratum' => $desideratum])

            {{-- Add page-break after each employee, unless it's the last one --}}
            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @endif

    <div class="footer-info">
        Raport wygenerowany przez system planoMAT - {{ $reportDate }}
    </div>
</body>
</html> 