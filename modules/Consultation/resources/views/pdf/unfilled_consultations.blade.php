<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Raport Nieuzupełnionych Konsultacji</title>
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
        .no-data {
            text-align: center;
            font-style: italic;
            color: #777;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Lista osób, które nie uzupełniły konsultacji</div>
        <div class="subtitle">
            Typ: {{ $consultationType === \Modules\Consultation\Enums\ConsultationType::Semester ? 'Konsultacje semestralne' : 'Konsultacje sesyjne' }}
        </div>
    </div>
    
    <div class="footer">
        <div class="left">Wygenerowano: {{ $reportDate }}</div>
        <div class="right"><span class="page-number"></span></div>
    </div>

    @if($unfilledWorkers->isEmpty())
        <p class="no-data">Wszyscy pracownicy uzupełnili konsultacje.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:10%;">Lp.</th>
                    <th style="width:90%;">Imię i Nazwisko</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unfilledWorkers as $index => $worker)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $worker->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html> 