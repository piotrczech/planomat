<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Consultation\Domain\Enums\ConsultationType;

final class AllConsultationsExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private readonly Collection $scientificWorkers,
        private readonly ConsultationType $consultationType,
    ) {
    }

    public function collection(): Collection
    {
        $data = collect();

        foreach ($this->scientificWorkers as $worker) {
            if ($this->consultationType === ConsultationType::Semester) {
                foreach ($worker->semesterConsultations ?? [] as $c) {
                    $data->push([
                        'worker' => $worker,
                        'type' => 'Semestralne',
                        'day' => $c->day->label(),
                        'week_type' => $c->week_type->label(),
                        'start_time' => $c->start_time->format('H:i'),
                        'end_time' => $c->end_time->format('H:i'),
                        'building' => $c->location_building,
                        'room' => $c->location_room,
                    ]);
                }

                foreach ($worker->partTimeConsultations ?? [] as $c) {
                    $data->push([
                        'worker' => $worker,
                        'type' => 'Zaoczne',
                        'day' => $c->consultation_date->format('Y-m-d'),
                        'week_type' => '-',
                        'start_time' => $c->start_time->format('H:i'),
                        'end_time' => $c->end_time->format('H:i'),
                        'building' => $c->location_building,
                        'room' => $c->location_room,
                    ]);
                }
            } elseif ($this->consultationType === ConsultationType::Session) {
                foreach ($worker->sessionConsultations ?? [] as $c) {
                    $data->push([
                        'worker' => $worker,
                        'date' => $c->consultation_date->format('Y-m-d'),
                        'start_time' => $c->start_time->format('H:i'),
                        'end_time' => $c->end_time->format('H:i'),
                        'building' => $c->location_building,
                        'room' => $c->location_room,
                    ]);
                }
            }
        }

        return $data;
    }

    public function headings(): array
    {
        if ($this->consultationType === ConsultationType::Semester) {
            return [
                'Pracownik',
                'Typ konsultacji',
                'Dzień / Data',
                'Typ tygodnia',
                'Godzina rozpoczęcia',
                'Godzina zakończenia',
                'Budynek',
                'Pokój',
            ];
        }

        return [
            'Pracownik',
            'Data',
            'Godzina rozpoczęcia',
            'Godzina zakończenia',
            'Budynek',
            'Pokój',
        ];
    }

    public function map($row): array
    {
        if ($this->consultationType === ConsultationType::Semester) {
            return [
                $row['worker']->fullName(),
                $row['type'],
                $row['day'],
                $row['week_type'],
                $row['start_time'],
                $row['end_time'],
                $row['building'],
                $row['room'],
            ];
        }

        return [
            $row['worker']->fullName(),
            $row['date'],
            $row['start_time'],
            $row['end_time'],
            $row['building'],
            $row['room'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        if ($this->consultationType === ConsultationType::Semester) {
            return [
                'A' => 30,
                'B' => 15,
                'C' => 15,
                'D' => 15,
                'E' => 18,
                'F' => 18,
                'G' => 15,
                'H' => 15,
            ];
        }

        return [
            'A' => 30,
            'B' => 15,
            'C' => 18,
            'D' => 18,
            'E' => 15,
            'F' => 15,
        ];
    }
}
