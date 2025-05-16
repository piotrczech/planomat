<?php

declare(strict_types=1);

namespace Modules\Desiderata\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Infrastructure\Models\Desideratum;
use App\Enums\CoursePreferenceTypeEnum;
use App\Enums\WeekdayEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Infrastructure\Models\DesideratumUnavailableTimeSlot;
use Modules\Desiderata\Infrastructure\Models\DesideratumCoursePreference;

class EloquentDesideratumRepository implements DesideratumRepositoryInterface
{
    public function findByScientificWorkerAndSemester(
        int $workerId,
        int $semesterId,
    ): ?UpdateOrCreateDesideratumDto {
        // Pobieramy desideratum z wszystkimi powiązanymi danymi
        $desideratum = Desideratum::where('scientific_worker_id', $workerId)
            ->where('semester_id', $semesterId)
            ->with([
                'coursePreferences', // Wszystkie preferencje kursów
                'unavailableTimeSlots', // Niedostępne sloty czasowe
            ])
            ->first();

        if (!$desideratum) {
            return null;
        }

        // Przygotowujemy dane do DTO zgodnie ze strukturą oczekiwaną przez komponenty Livewire

        // Przygotowujemy preferencje kursów pogrupowane według typów
        $coursePreferencesByType = $desideratum->coursePreferences->groupBy('type');

        $wantedCourseIds = $coursePreferencesByType->get(CoursePreferenceTypeEnum::WANTED->value, collect())
            ->pluck('course_id')
            ->toArray();

        $proficientCourseIds = $coursePreferencesByType->get(CoursePreferenceTypeEnum::COULD->value, collect())
            ->pluck('course_id')
            ->toArray();

        $unwantedCourseIds = $coursePreferencesByType->get(CoursePreferenceTypeEnum::UNWANTED->value, collect())
            ->pluck('course_id')
            ->toArray();

        // Grupujemy niedostępne sloty czasowe według dni tygodnia
        $unavailableTimeSlots = [];

        foreach ($desideratum->unavailableTimeSlots as $slot) {
            $dayValue = $slot->day->value;

            if (!isset($unavailableTimeSlots[$dayValue])) {
                $unavailableTimeSlots[$dayValue] = [];
            }
            $unavailableTimeSlots[$dayValue][] = $slot->time_slot_id;
        }

        // Tworzymy i zwracamy obiekt DTO z wszystkimi danymi
        return UpdateOrCreateDesideratumDto::from([
            // Dane z głównej tabeli desideratum
            'wantStationary' => $desideratum->want_stationary,
            'wantNonStationary' => $desideratum->want_non_stationary,
            'agreeToOvertime' => $desideratum->agree_to_overtime,
            'masterThesesCount' => $desideratum->master_theses_count,
            'bachelorThesesCount' => $desideratum->bachelor_theses_count,
            'maxHoursPerDay' => $desideratum->max_hours_per_day,
            'maxConsecutiveHours' => $desideratum->max_consecutive_hours,
            'additionalNotes' => $desideratum->additional_notes,

            // Pogrupowane preferencje kursów
            'wantedCourseIds' => $wantedCourseIds,
            'proficientCourseIds' => $proficientCourseIds,
            'unwantedCourseIds' => $unwantedCourseIds,

            // Niedostępne sloty czasowe
            'unavailableTimeSlots' => $unavailableTimeSlots,
        ]);
    }

    public function updateOrCreate(UpdateOrCreateDesideratumDto $dto): int
    {
        return DB::transaction(function () use ($dto) {
            // Utwórz lub zaktualizuj główny rekord Desideratum
            $desideratum = Desideratum::updateOrCreate(
                [
                    'semester_id' => 1,
                    'scientific_worker_id' => Auth::user()->id,
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

            // Czyścimy wszystkie istniejące preferencje kursów
            DesideratumCoursePreference::where('desideratum_id', $desideratum->id)->delete();

            // Dodajemy nowe preferencje kursów
            $this->addCoursePreferences($desideratum, $dto);

            // Synchronizuj niedostępne sloty czasowe
            $this->syncUnavailableTimeSlots($desideratum, $dto);

            return $desideratum->id;
        });
    }

    /**
     * Dodaje preferencje kursów dla desideratum
     */
    private function addCoursePreferences(Desideratum $desideratum, UpdateOrCreateDesideratumDto $dto): void
    {
        $this->addCoursePreferencesForType($desideratum->id, $dto->wantedCourseIds ?? [], CoursePreferenceTypeEnum::WANTED);
        $this->addCoursePreferencesForType($desideratum->id, $dto->proficientCourseIds ?? [], CoursePreferenceTypeEnum::COULD);
        $this->addCoursePreferencesForType($desideratum->id, $dto->unwantedCourseIds ?? [], CoursePreferenceTypeEnum::UNWANTED);
    }

    /**
     * Dodaje preferencje kursów określonego typu
     */
    private function addCoursePreferencesForType(int $desideratumId, array $courseIds, CoursePreferenceTypeEnum $type): void
    {
        foreach ($courseIds as $courseId) {
            if (empty($courseId) || !is_numeric($courseId)) {
                continue;
            }

            DB::table('desideratum_course_preferences')->insert([
                'desideratum_id' => $desideratumId,
                'course_id' => (int) $courseId,
                'type' => $type,
                'updated_at' => now(),
            ]);
        }
    }

    private function syncUnavailableTimeSlots(Desideratum $desideratum, UpdateOrCreateDesideratumDto $dto): void
    {
        $newSlots = collect();

        if (!empty($dto->unavailableTimeSlots) && is_array($dto->unavailableTimeSlots)) {
            foreach ($dto->unavailableTimeSlots as $day => $timeSlotIds) {
                if (!is_array($timeSlotIds) || empty($timeSlotIds)) {
                    continue;
                }

                $weekDay = $this->mapDayNameToEnum($day);

                if ($weekDay === null) {
                    continue;
                }

                foreach ($timeSlotIds as $timeSlotId) {
                    if (empty($timeSlotId) || !is_numeric($timeSlotId)) {
                        continue;
                    }

                    $newSlots->push([
                        'day' => $weekDay->value,
                        'time_slot_id' => (int) $timeSlotId,
                        'slot_key' => $weekDay->value . '_' . (int) $timeSlotId,
                    ]);
                }
            }
        }

        // Pobieramy istniejące sloty
        $existingSlots = $desideratum->unavailableTimeSlots()
            ->get()
            ->map(fn (DesideratumUnavailableTimeSlot $slot) => [
                'id' => $slot->id,
                'day' => $slot->day,
                'time_slot_id' => $slot->time_slot_id,
                'slot_key' => "{$slot->day->value}_{$slot->time_slot_id}",
            ]);

        // Znajdujemy sloty do usunięcia
        $slotsToDelete = $existingSlots
            ->whereNotIn('slot_key', $newSlots->pluck('slot_key'))
            ->pluck('id');

        // Znajdujemy sloty do dodania (te, które nie istnieją)
        $slotsToAdd = $newSlots
            ->whereNotIn('slot_key', $existingSlots->pluck('slot_key'))
            ->map(fn (array $slot) => [
                'day' => $slot['day'],
                'time_slot_id' => $slot['time_slot_id'],
            ]);

        // Usuwamy niepotrzebne sloty
        if ($slotsToDelete->isNotEmpty()) {
            $desideratum->unavailableTimeSlots()
                ->whereIn('id', $slotsToDelete)
                ->delete();
        }

        // Dodajemy nowe sloty
        if ($slotsToAdd->isNotEmpty()) {
            $desideratum->unavailableTimeSlots()->createMany($slotsToAdd);
        }
    }

    /**
     * Mapuje nazwę dnia na odpowiadający mu enum WeekdayEnum
     */
    private function mapDayNameToEnum(string $dayName): ?WeekdayEnum
    {
        return match (mb_strtolower($dayName)) {
            'monday' => WeekdayEnum::MONDAY,
            'tuesday' => WeekdayEnum::TUESDAY,
            'wednesday' => WeekdayEnum::WEDNESDAY,
            'thursday' => WeekdayEnum::THURSDAY,
            'friday' => WeekdayEnum::FRIDAY,
            'saturday' => WeekdayEnum::SATURDAY,
            'sunday' => WeekdayEnum::SUNDAY,
            default => null,
        };
    }
}
