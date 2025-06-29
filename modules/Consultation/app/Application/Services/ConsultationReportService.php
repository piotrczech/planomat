<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\Services;

use Illuminate\Support\Collection;
use Modules\Consultation\Domain\Enums\ConsultationType;
use App\Domain\Enums\WeekTypeEnum;

final class ConsultationReportService
{
    public function prepareAllConsultationsReportData(Collection $scientificWorkers, ConsultationType $consultationType): array
    {
        $processedWorkers = [];

        foreach ($scientificWorkers as $worker) {
            $allLines = [];

            // Logic for semester and part-time consultations
            if ($consultationType === ConsultationType::Semester) {
                if (isset($worker->semesterConsultations)) {
                    foreach ($worker->semesterConsultations as $c) {
                        $schedule = $c->day->label();

                        if ($c->week_type !== WeekTypeEnum::ALL) {
                            $schedule .= ' ' . $c->week_type->shortLabel();
                        }
                        $schedule .= ', ' . $c->start_time->format('H:i') . ' - ' . $c->end_time->format('H:i');
                        $location = e($c->location_building) . ($c->location_room ? ',&nbsp;' . e($c->location_room) : '');
                        $allLines[] = ['schedule' => $schedule, 'location' => $location, 'is_html' => false];
                    }
                }

                if (isset($worker->partTimeConsultations) && $worker->partTimeConsultations->isNotEmpty()) {
                    $groupedByLocation = $worker->partTimeConsultations->groupBy(fn ($item) => $item->location_building . '##' . $item->location_room);

                    foreach ($groupedByLocation as $locationKey => $consultationsInLocation) {
                        $locationParts = explode('##', $locationKey);
                        $location = e($locationParts[0]) . (!empty($locationParts[1]) ? ',&nbsp;' . e($locationParts[1]) : '');

                        $groupedByDay = $consultationsInLocation->sortBy('consultation_date')->groupBy(fn ($c) => $c->consultation_date->format('N'));

                        $daySchedules = [];

                        foreach ($groupedByDay as $dayOfWeek => $dayConsultations) {
                            $dayName = $dayConsultations->first()->consultation_date->locale(config('app.locale'))->dayName;
                            $timeStrings = $dayConsultations->map(fn ($c) => $c->consultation_date->format('d.m') . ' ' . $c->start_time->format('H:i') . '-' . $c->end_time->format('H:i'))->implode('; ');
                            $daySchedules[] = e(ucfirst($dayName) . ': ' . $timeStrings);
                        }
                        $allLines[] = ['schedule' => implode('<br>', $daySchedules), 'location' => $location, 'is_html' => true];
                    }
                }
            }

            // Logic for session consultations
            if ($consultationType === ConsultationType::Session && isset($worker->sessionConsultations)) {
                foreach ($worker->sessionConsultations as $c) {
                    $schedule = $c->consultation_date->format('d.m.Y') . ', ' . $c->start_time->format('H:i') . ' - ' . $c->end_time->format('H:i');
                    $location = e($c->location_building) . ($c->location_room ? ',&nbsp;' . e($c->location_room) : '');
                    $allLines[] = ['schedule' => $schedule, 'location' => $location, 'is_html' => false];
                }
            }

            if (count($allLines) > 0) {
                $processedWorkers[] = [
                    'name' => $worker->name,
                    'lines' => $allLines,
                ];
            }
        }

        return $processedWorkers;
    }
}
