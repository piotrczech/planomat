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
        .cover-page {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
        }
        .cover-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .cover-subtitle {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .cover-date {
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="cover-page">
        <div class="cover-title">Dezyderaty pracowników Wydziału Matematyki</div>
        <div class="cover-subtitle">dla semestru: <strong>{{ $semester->name }} ({{ $semester->academic_year }})</strong></div>
        <div class="cover-date">Data wygenerowania raportu: {{ $reportDate }}</div>
    </div>
    <div class="page-break"></div>

    <div class="report-generated-date">Wygenerowano: {{ $reportDate }}</div>

    @if($scientificWorkers->isEmpty())
        <div class="no-data-info">
            Brak pracowników naukowych do wyświetlenia w raporcie dla tego semestru.
        </div>
    @else
        @foreach($scientificWorkers as $worker)
            @php
                $desideratum = $worker->desiderata->first();
                if (!$desideratum) {
                    $desideratum = new \Modules\Desiderata\Infrastructure\Models\Desideratum();
                    $desideratum->setRelation('scientificWorker', $worker);
                }
            @endphp
            @include('desiderata::pdf.partials.desiderata_preferences_page', ['desideratum' => $desideratum])
            <div class="page-break"></div>
            @include('desiderata::pdf.partials.desiderata_availability_page', ['desideratum' => $desideratum])

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