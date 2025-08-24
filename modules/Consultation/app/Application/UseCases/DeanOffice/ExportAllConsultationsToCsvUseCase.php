<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\DeanOffice;

use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Domain\Enums\ConsultationType;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

final class ExportAllConsultationsToCsvUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
    ) {
    }

    public function execute(int $semesterId, string $type): StreamedResponse
    {
        $consultationType = ConsultationType::tryFrom($type);

        if (!$consultationType || !in_array($consultationType, [ConsultationType::Semester, ConsultationType::Session])) {
            throw ValidationException::withMessages(['type' => 'Invalid consultation type provided for CSV export.']);
        }

        $scientificWorkers = $this->consultationRepository->getAllScientificWorkersWithConsultations($semesterId, $consultationType);

        $filename = 'raport_konsultacji_' . $type . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($scientificWorkers, $consultationType): void {
            $file = fopen('php://output', 'w');
            fwrite($file, (chr(0xEF) . chr(0xBB) . chr(0xBF))); // Add UTF-8 BOM

            if ($consultationType === ConsultationType::Semester) {
                fputcsv($file, [
                    'Pracownik', 'Typ konsultacji', 'Dzień / Data', 'Typ tygodnia', 'Godzina rozpoczęcia', 'Godzina zakończenia', 'Budynek', 'Pokój',
                ]);
            } else { // Session
                fputcsv($file, [
                    'Pracownik', 'Data', 'Godzina rozpoczęcia', 'Godzina zakończenia', 'Budynek', 'Pokój',
                ]);
            }

            foreach ($scientificWorkers as $worker) {
                if ($consultationType === ConsultationType::Semester) {
                    foreach ($worker->semesterConsultations ?? [] as $c) {
                        fputcsv($file, [
                            $worker->fullName(),
                            'Semestralne',
                            $c->day->label(),
                            $c->week_type->label(),
                            $c->start_time->format('H:i'),
                            $c->end_time->format('H:i'),
                            $c->location_building,
                            $c->location_room,
                        ]);
                    }

                    foreach ($worker->partTimeConsultations ?? [] as $c) {
                        fputcsv($file, [
                            $worker->fullName(),
                            'Zaoczne',
                            $c->consultation_date->format('Y-m-d'),
                            '-',
                            $c->start_time->format('H:i'),
                            $c->end_time->format('H:i'),
                            $c->location_building,
                            $c->location_room,
                        ]);
                    }
                } elseif ($consultationType === ConsultationType::Session) {
                    foreach ($worker->sessionConsultations ?? [] as $c) {
                        fputcsv($file, [
                            $worker->fullName(),
                            $c->consultation_date->format('Y-m-d'),
                            $c->start_time->format('H:i'),
                            $c->end_time->format('H:i'),
                            $c->location_building,
                            $c->location_room,
                        ]);
                    }
                }
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
