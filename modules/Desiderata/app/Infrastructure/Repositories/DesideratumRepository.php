<?php

declare(strict_types=1);

namespace Modules\Desiderata\Infrastructure\Repositories;

use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Infrastructure\Models\Desideratum;
use App\Enums\CoursePreferenceTypeEnum;
use Illuminate\Support\Collection;
use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Infrastructure\Models\DesideratumUnavailableTimeSlot;
use Modules\Desiderata\Infrastructure\Models\DesideratumCoursePreference;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\Course;

class DesideratumRepository implements DesideratumRepositoryInterface
{
    public function findByScientificWorkerAndSemester(
        int $workerId,
        int $semesterId,
    ): ?UpdateOrCreateDesideratumDto {
        $desideratum = Desideratum::where('scientific_worker_id', $workerId)
            ->where('semester_id', $semesterId)
            ->with([
                'coursePreferences',
                'unavailableTimeSlots',
            ])
            ->first();

        if (!$desideratum) {
            return null;
        }

        $coursePreferencesByType = $desideratum->coursePreferences->groupBy('type');

        $wantedCourseIds = $coursePreferencesByType->get(CoursePreferenceTypeEnum::WANTED->value, collect())
            ->pluck('course_id')
            ->toArray();

        $unwantedCourseIds = $coursePreferencesByType->get(CoursePreferenceTypeEnum::UNWANTED->value, collect())
            ->pluck('course_id')
            ->toArray();

        $proficientCourseIds = Course::whereNotIn('id', array_merge($wantedCourseIds, $unwantedCourseIds))
            ->pluck('id')
            ->toArray();

        $unavailableTimeSlots = [];

        foreach ($desideratum->unavailableTimeSlots as $slot) {
            $dayValue = $slot->day->value;

            if (!isset($unavailableTimeSlots[$dayValue])) {
                $unavailableTimeSlots[$dayValue] = [];
            }
            $unavailableTimeSlots[$dayValue][] = $slot->time_slot_id;
        }

        return UpdateOrCreateDesideratumDto::from([
            'wantStationary' => $desideratum->want_stationary,
            'wantNonStationary' => $desideratum->want_non_stationary,
            'agreeToOvertime' => $desideratum->agree_to_overtime,
            'masterThesesCount' => $desideratum->master_theses_count,
            'bachelorThesesCount' => $desideratum->bachelor_theses_count,
            'maxHoursPerDay' => $desideratum->max_hours_per_day,
            'maxConsecutiveHours' => $desideratum->max_consecutive_hours,
            'additionalNotes' => $desideratum->additional_notes,

            'wantedCourseIds' => $wantedCourseIds,
            'proficientCourseIds' => $proficientCourseIds,
            'unwantedCourseIds' => $unwantedCourseIds,

            'unavailableTimeSlots' => $unavailableTimeSlots,
        ]);
    }

    public function getDesideratumForUserAndSemester(int $userId, int $semesterId): ?Desideratum
    {
        return Desideratum::where('scientific_worker_id', $userId)
            ->where('semester_id', $semesterId)
            ->first();
    }

    public function updateOrCreate(UpdateOrCreateDesideratumDto $dto, User $user, int $semesterId): Desideratum
    {
        $desideratum = Desideratum::updateOrCreate(
            [
                'scientific_worker_id' => $user->id,
                'semester_id' => $semesterId,
            ],
            [
                'want_stationary' => $dto->wantStationary,
                'want_non_stationary' => $dto->wantNonStationary,
                'agree_to_overtime' => $dto->agreeToOvertime,
                'master_theses_count' => $dto->masterThesesCount,
                'bachelor_theses_count' => $dto->bachelorThesesCount,
                'max_hours_per_day' => $dto->maxHoursPerDay,
                'max_consecutive_hours' => $dto->maxConsecutiveHours,
                'additional_notes' => $dto->additionalNotes,
            ],
        );

        $desideratum->unavailableTimeSlots()->delete();

        if (!empty($dto->unavailableTimeSlots)) {
            $slotsToInsert = [];

            foreach ($dto->unavailableTimeSlots as $dayString => $timeSlotIds) {
                if (empty($timeSlotIds)) {
                    continue;
                }

                foreach ($timeSlotIds as $slotId) {
                    $slotsToInsert[] = [
                        'desideratum_id' => $desideratum->id,
                        'day' => $dayString,
                        'time_slot_id' => $slotId,
                    ];
                }
            }

            if (!empty($slotsToInsert)) {
                DesideratumUnavailableTimeSlot::insert($slotsToInsert);
            }
        }

        $desideratum->coursePreferences()->delete();

        $preferencesToInsert = [];
        $courseIdToType = [];

        foreach ($dto->wantedCourseIds as $courseId) {
            $courseIdToType[$courseId] = CoursePreferenceTypeEnum::WANTED->value;
        }

        foreach ($dto->unwantedCourseIds as $courseId) {
            $courseIdToType[$courseId] = CoursePreferenceTypeEnum::UNWANTED->value;
        }

        foreach ($dto->proficientCourseIds as $courseId) {
            if (!isset($courseIdToType[$courseId])) {
                $courseIdToType[$courseId] = CoursePreferenceTypeEnum::COULD->value;
            }
        }

        foreach ($courseIdToType as $courseId => $type) {
            $preferencesToInsert[] = [
                'desideratum_id' => $desideratum->id,
                'course_id' => $courseId,
                'type' => $type,
            ];
        }

        if (!empty($preferencesToInsert)) {
            DesideratumCoursePreference::insert($preferencesToInsert);
        }

        return $desideratum;
    }

    public function getLastUpdateDate(int $userId): ?string
    {
        return Desideratum::where('scientific_worker_id', $userId)
            ->latest('updated_at')
            ->value('updated_at')
            ?->toDateTimeString();
    }

    public function getAllDesiderataForPdfExport(int $semesterId): Collection
    {
        return User::where('role', RoleEnum::SCIENTIFIC_WORKER)
            ->with(['desiderata' => function ($query) use ($semesterId): void {
                $query->where('semester_id', $semesterId)
                    ->with(['unavailableTimeSlots', 'coursePreferences.course']);
            }])
            ->orderBy('name')
            ->get();
    }

    public function getScientificWorkersWithoutDesiderata(int $semesterId): Collection
    {
        return User::where('role', RoleEnum::SCIENTIFIC_WORKER)
            ->whereDoesntHave('desiderata', function ($query) use ($semesterId): void {
                $query->where('semester_id', $semesterId);
            })
            ->orderBy('name')
            ->get();
    }
}
