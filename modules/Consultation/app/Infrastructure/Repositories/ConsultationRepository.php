<?php

declare(strict_types=1);

namespace Modules\Consultation\Infrastructure\Repositories;

use App\Domain\Enums\RoleEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Domain\Enums\ConsultationType;
use Modules\Consultation\Infrastructure\Models\ConsultationSemester;
use Modules\Consultation\Infrastructure\Models\ConsultationSession;
use App\Domain\Enums\WeekdayEnum;
use App\Domain\Enums\WeekTypeEnum;
use App\Infrastructure\Models\Semester;
use App\Infrastructure\Models\User;

final class ConsultationRepository implements ConsultationRepositoryInterface
{
    public function createNewSemesterConsultation(CreateNewSemesterConsultationDto $dto): int
    {
        $scientificWorkerId = Auth::id();
        $currentSemesterId = Semester::getCurrentSemester()->id;

        if (in_array($dto->consultationWeekday, [
            WeekdayEnum::MONDAY->value,
            WeekdayEnum::TUESDAY->value,
            WeekdayEnum::WEDNESDAY->value,
            WeekdayEnum::THURSDAY->value,
            WeekdayEnum::FRIDAY->value,
        ])) {
            return $this->createWeekdayConsultation(
                $scientificWorkerId,
                $currentSemesterId,
                $dto,
            ) ? 1 : 0;
        }

        return $this->createWeekendConsultations(
            $scientificWorkerId,
            $currentSemesterId,
            $dto,
        );
    }

    public function createWeekdayConsultation(
        int $scientificWorkerId,
        int $semesterId,
        CreateNewSemesterConsultationDto $dto,
    ): bool {
        $consultation = ConsultationSemester::create([
            'scientific_worker_id' => $scientificWorkerId,
            'semester_id' => $semesterId,
            'day' => $dto->consultationWeekday,
            'week_type' => $dto->dailyConsultationWeekType,
            'start_time' => $dto->consultationStartTime,
            'end_time' => $dto->consultationEndTime,
            'location' => $dto->consultationLocation,
        ]);

        return $consultation->exists;
    }

    public function createWeekendConsultations(
        int $scientificWorkerId,
        int $semesterId,
        CreateNewSemesterConsultationDto $dto,
    ): int {
        if (empty($dto->weeklyConsultationDates)) {
            return 0;
        }

        $weekday = $dto->consultationWeekday === WeekdayEnum::SATURDAY->value ? WeekdayEnum::SATURDAY->value : WeekdayEnum::SUNDAY->value;

        $consultation = ConsultationSemester::create([
            'scientific_worker_id' => $scientificWorkerId,
            'semester_id' => $semesterId,
            'day' => $weekday,
            'week_type' => null,
            'weekend_consultation_dates' => $dto->weeklyConsultationDates,
            'start_time' => $dto->consultationStartTime,
            'end_time' => $dto->consultationEndTime,
            'location' => $dto->consultationLocation,
        ]);

        return $consultation->exists ? 1 : 0;
    }

    public function getSemesterConsultations(int $scientificWorkerId, int $semesterId): array
    {
        $consultations = ConsultationSemester::where('scientific_worker_id', $scientificWorkerId)
            ->where('semester_id', $semesterId)
            ->get();

        return $consultations->map(function ($consultation) {
            $weekdayNumber = match ($consultation->day->value) {
                WeekdayEnum::MONDAY->value => 0,
                WeekdayEnum::TUESDAY->value => 1,
                WeekdayEnum::WEDNESDAY->value => 2,
                WeekdayEnum::THURSDAY->value => 3,
                WeekdayEnum::FRIDAY->value => 4,
                WeekdayEnum::SATURDAY->value => 5,
                WeekdayEnum::SUNDAY->value => 6,
                default => 0,
            };

            $weekTypeString = match ($consultation->week_type?->value ?? null) {
                WeekTypeEnum::ALL->value => 'every',
                WeekTypeEnum::EVEN->value => 'even',
                WeekTypeEnum::ODD->value => 'odd',
                null => null,
                default => 'every',
            };

            return [
                'id' => $consultation->id,
                'weekday' => $weekdayNumber,
                'startTime' => $consultation->start_time->format('H:i'),
                'endTime' => $consultation->end_time->format('H:i'),
                'location' => $consultation->location,
                'weekType' => $weekTypeString,
                'weekendConsultationDates' => $consultation->weekend_consultation_dates,
            ];
        })->toArray();
    }

    public function deleteSemesterConsultation(int $consultationId, int $scientificWorkerId): bool
    {
        $consultation = ConsultationSemester::where('id', $consultationId)
            ->where('scientific_worker_id', $scientificWorkerId)
            ->first();

        if (!$consultation) {
            return false;
        }

        return (bool) $consultation->delete();
    }

    public function createSessionConsultation(
        int $scientificWorkerId,
        int $semesterId,
        CreateNewSessionConsultationDto $dto,
    ): int {
        $consultation = ConsultationSession::create([
            'scientific_worker_id' => $scientificWorkerId,
            'semester_id' => $semesterId,
            'consultation_date' => $dto->consultationDate,
            'start_time' => $dto->consultationStartTime,
            'end_time' => $dto->consultationEndTime,
            'location' => $dto->consultationLocation,
        ]);

        return $consultation->exists ? $consultation->id : 0;
    }

    public function getSessionConsultations(int $scientificWorkerId, int $semesterId): array
    {
        $consultations = ConsultationSession::where('scientific_worker_id', $scientificWorkerId)
            ->where('semester_id', $semesterId)
            ->orderBy('consultation_date')
            ->orderBy('start_time')
            ->get();

        return $consultations->map(function ($consultation) {
            return [
                'id' => $consultation->id,
                'consultation_date' => Carbon::parse($consultation->consultation_date)->toDateString(),
                'start_time' => Carbon::parse($consultation->start_time)->format('H:i'),
                'end_time' => Carbon::parse($consultation->end_time)->format('H:i'),
                'location' => $consultation->location,
            ];
        })->toArray();
    }

    public function deleteSessionConsultation(int $consultationId, int $scientificWorkerId): bool
    {
        $consultation = ConsultationSession::where('id', $consultationId)
            ->where('scientific_worker_id', $scientificWorkerId)
            ->first();

        if (!$consultation) {
            return false;
        }

        return (bool) $consultation->delete();
    }

    public function getLastSemesterConsultationUpdateDate(int $scientificWorkerId): ?string
    {
        $latestConsultation = ConsultationSemester::where('scientific_worker_id', $scientificWorkerId)
            ->orderByDesc('updated_at')
            ->first();

        return $latestConsultation?->updated_at?->toDateString();
    }

    public function getLastSessionConsultationUpdateDate(int $scientificWorkerId): ?string
    {
        $latestConsultation = ConsultationSession::where('scientific_worker_id', $scientificWorkerId)
            ->orderByDesc('updated_at')
            ->first();

        return $latestConsultation?->updated_at?->toDateString();
    }

    public function getConsultationSummaryTime(int $scientificWorkerId): ?string
    {
        $consultations = ConsultationSemester::where('scientific_worker_id', $scientificWorkerId)
            ->whereNotIn('day', [WeekdayEnum::SATURDAY->value, WeekdayEnum::SUNDAY->value])
            ->get();

        $totalDuration = 0;

        foreach ($consultations as $consultation) {
            $startTime = Carbon::parse($consultation->start_time);
            $endTime = Carbon::parse($consultation->end_time);

            $totalDuration += $startTime->diffInMinutes($endTime);
        }

        if ($totalDuration % 60 === 0) {
            return sprintf('%d h', floor($totalDuration / 60));
        }

        return sprintf('%d h %d min', floor($totalDuration / 60), $totalDuration % 60);
    }

    public function getAllScientificWorkersWithConsultations(int $semesterId, ConsultationType $type): Collection
    {
        $consultationRelation = match ($type) {
            ConsultationType::Semester => 'semesterConsultations',
            ConsultationType::Session => 'sessionConsultations',
        };

        return User::where('role', RoleEnum::SCIENTIFIC_WORKER)
            ->with([
                $consultationRelation => function ($query) use ($semesterId): void {
                    $query->where('semester_id', $semesterId)
                        ->orderBy('start_time');
                },
            ])
            ->orderBy('name')
            ->get();
    }

    public function fetchAllForPdfExportBySemesterAndType(int $semesterId, ConsultationType $type): Collection
    {
        return $this->getAllScientificWorkersWithConsultations($semesterId, $type);
    }

    public function getScientificWorkersWithoutConsultations(int $semesterId, ConsultationType $type): Collection
    {
        $consultationRelation = match ($type) {
            ConsultationType::Semester => 'semesterConsultations',
            ConsultationType::Session => 'sessionConsultations',
        };

        return User::where('role', RoleEnum::SCIENTIFIC_WORKER)
            ->whereDoesntHave($consultationRelation, function ($query) use ($semesterId): void {
                $query->where('semester_id', $semesterId);
            })
            ->orderBy('name')
            ->get();
    }
}
