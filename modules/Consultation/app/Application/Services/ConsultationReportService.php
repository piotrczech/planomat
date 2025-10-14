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
                    // Group consultations by day and location
                    $groupedConsultations = $worker->semesterConsultations->groupBy(function ($c) {
                        return $c->day->value . '|' . $c->location_building . '|' . ($c->location_room ?? '');
                    });

                    foreach ($groupedConsultations as $groupKey => $consultations) {
                        $parts = explode('|', $groupKey);
                        $day = $consultations->first()->day->label();
                        $location = e($parts[1]) . (!empty($parts[2]) ? ',&nbsp;' . e($parts[2]) : '');

                        // Group by week type within the same day and location
                        $weekTypeGroups = $consultations->groupBy('week_type');
                        $scheduleParts = [];

                        foreach ($weekTypeGroups as $weekTypeKey => $weekConsultations) {
                            $timeRanges = $weekConsultations->map(function ($c) {
                                return $c->start_time->format('H:i') . ' - ' . $c->end_time->format('H:i');
                            })->implode('; ');

                            $weekType = $weekConsultations->first()->week_type;
                            $schedulePart = $timeRanges;

                            if ($weekType !== WeekTypeEnum::ALL) {
                                $schedulePart .= ' (' . $weekType->shortLabel() . ')';
                            }

                            $scheduleParts[] = $schedulePart;
                        }

                        $schedule = $day . ', ' . implode('; ', $scheduleParts);
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
                // Group session consultations by date and location
                $groupedSessionConsultations = $worker->sessionConsultations->groupBy(function ($c) {
                    return $c->consultation_date->format('Y-m-d') . '|' . $c->location_building . '|' . ($c->location_room ?? '');
                });

                foreach ($groupedSessionConsultations as $groupKey => $consultations) {
                    $parts = explode('|', $groupKey);
                    $date = $consultations->first()->consultation_date->format('d.m.Y');
                    $location = e($parts[1]) . (!empty($parts[2]) ? ',&nbsp;' . e($parts[2]) : '');

                    $timeRanges = $consultations->map(function ($c) {
                        return $c->start_time->format('H:i') . ' - ' . $c->end_time->format('H:i');
                    })->implode('; ');

                    $schedule = $date . ', ' . $timeRanges;
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
