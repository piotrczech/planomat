<?php

declare(strict_types=1);

namespace Modules\Consultation\Infrastructure\Repositories;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use App\Domain\Enums\RoleEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Dto\CreateNewPartTimeConsultationDto;
use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Domain\Enums\ConsultationType;
use Modules\Consultation\Infrastructure\Models\PartTimeConsultation;
use Modules\Consultation\Infrastructure\Models\SemesterConsultation;
use Modules\Consultation\Infrastructure\Models\SessionConsultation;
use App\Domain\Enums\WeekdayEnum;
use App\Infrastructure\Models\User;
use Exception;

final class ConsultationRepository implements ConsultationRepositoryInterface
{
    private GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase;

    public function __construct(GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase)
    {
        $this->getActiveConsultationSemesterUseCase = $getActiveConsultationSemesterUseCase;
    }

    public function createNewSemesterConsultation(CreateNewSemesterConsultationDto $dto): int
    {
        $scientificWorkerId = Auth::id();
        $currentSemester = $this->getActiveConsultationSemesterUseCase->execute();

        if (!$currentSemester) {
            // Or throw an exception
            return 0;
        }

        $consultation = SemesterConsultation::create([
            'scientific_worker_id' => $scientificWorkerId,
            'semester_id' => $currentSemester->id,
            'day' => $dto->consultationWeekday,
            'week_type' => $dto->dailyConsultationWeekType,
            'start_time' => $dto->consultationStartTime,
            'end_time' => $dto->consultationEndTime,
            'location_building' => $dto->consultationLocationBuilding,
            'location_room' => $dto->consultationLocationRoom,
        ]);

        return $consultation->exists ? 1 : 0;
    }

    public function getSemesterConsultations(int $scientificWorkerId, int $semesterId): array
    {
        $consultations = SemesterConsultation::where('scientific_worker_id', $scientificWorkerId)
            ->where('semester_id', $semesterId)
            ->get();

        return $consultations->map(function ($consultation) {
            return [
                'id' => $consultation->id,
                'weekday' => $consultation->day->value,
                'startTime' => $consultation->start_time->format('H:i'),
                'endTime' => $consultation->end_time->format('H:i'),
                'locationBuilding' => $consultation->location_building,
                'locationRoom' => $consultation->location_room,
                'weekType' => $consultation->week_type->value,
            ];
        })->toArray();
    }

    public function deleteSemesterConsultation(int $consultationId, int $scientificWorkerId): bool
    {
        if (empty($scientificWorkerId)) {
            throw new Exception('Scientific worker ID is required');
        }

        $consultation = SemesterConsultation::where('id', $consultationId)
            ->where('scientific_worker_id', $scientificWorkerId)
            ->first();

        if (!$consultation) {
            return false;
        }

        return (bool) $consultation->delete();
    }

    public function createNewSessionConsultation(CreateNewSessionConsultationDto $dto): int
    {
        $scientificWorkerId = Auth::id();
        $currentSemester = $this->getActiveConsultationSemesterUseCase->execute();

        if (!$currentSemester) {
            return 0;
        }

        $consultation = SessionConsultation::create([
            'scientific_worker_id' => $scientificWorkerId,
            'semester_id' => $currentSemester->id,
            'consultation_date' => $dto->consultationDate,
            'start_time' => $dto->consultationStartTime,
            'end_time' => $dto->consultationEndTime,
            'location_building' => $dto->consultationLocationBuilding,
            'location_room' => $dto->consultationLocationRoom,
        ]);

        return $consultation->exists ? $consultation->id : 0;
    }

    public function getSessionConsultations(int $scientificWorkerId, int $semesterId): array
    {
        $consultations = SessionConsultation::where('scientific_worker_id', $scientificWorkerId)
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
                'locationBuilding' => $consultation->location_building,
                'locationRoom' => $consultation->location_room,
            ];
        })->toArray();
    }

    public function deleteSessionConsultation(int $consultationId, int $scientificWorkerId): bool
    {
        if (empty($scientificWorkerId)) {
            throw new Exception('Scientific worker ID is required');
        }

        $consultation = SessionConsultation::where('id', $consultationId)
            ->where('scientific_worker_id', $scientificWorkerId)
            ->first();

        if (!$consultation) {
            return false;
        }

        return (bool) $consultation->delete();
    }

    public function createNewPartTimeConsultation(CreateNewPartTimeConsultationDto $dto): int
    {
        $scientificWorkerId = Auth::id();
        $currentSemester = $this->getActiveConsultationSemesterUseCase->execute();

        if (!$currentSemester) {
            return 0;
        }

        $consultation = PartTimeConsultation::create([
            'scientific_worker_id' => $scientificWorkerId,
            'semester_id' => $currentSemester->id,
            'consultation_date' => $dto->consultationDate,
            'start_time' => $dto->consultationStartTime,
            'end_time' => $dto->consultationEndTime,
            'location_building' => $dto->consultationLocationBuilding,
            'location_room' => $dto->consultationLocationRoom,
        ]);

        return $consultation->exists ? $consultation->id : 0;
    }

    public function getPartTimeConsultations(int $scientificWorkerId, int $semesterId): array
    {
        $consultations = PartTimeConsultation::where('scientific_worker_id', $scientificWorkerId)
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
                'locationBuilding' => $consultation->location_building,
                'locationRoom' => $consultation->location_room,
            ];
        })->toArray();
    }

    public function deletePartTimeConsultation(int $consultationId, int $scientificWorkerId): bool
    {
        if (empty($scientificWorkerId)) {
            throw new Exception('Scientific worker ID is required');
        }

        $consultation = PartTimeConsultation::where('id', $consultationId)
            ->where('scientific_worker_id', $scientificWorkerId)
            ->first();

        if (!$consultation) {
            return false;
        }

        return (bool) $consultation->delete();
    }

    public function getLastUpdateDateForSemesterConsultation(int $scientificWorkerId, int $semesterId): ?string
    {
        $latestConsultation = SemesterConsultation::where('scientific_worker_id', $scientificWorkerId)
            ->where('semester_id', $semesterId)
            ->orderByDesc('updated_at')
            ->first();

        return $latestConsultation?->updated_at?->toDateString();
    }

    public function getLastUpdateDateForSessionConsultation(int $scientificWorkerId, int $semesterId): ?string
    {
        $latestConsultation = SessionConsultation::where('scientific_worker_id', $scientificWorkerId)
            ->where('semester_id', $semesterId)
            ->orderByDesc('updated_at')
            ->first();

        return $latestConsultation?->updated_at?->toDateString();
    }

    public function getLastUpdateDateForPartTimeConsultation(int $scientificWorkerId, int $semesterId): ?string
    {
        $latestConsultation = PartTimeConsultation::where('scientific_worker_id', $scientificWorkerId)
            ->where('semester_id', $semesterId)
            ->orderByDesc('updated_at')
            ->first();

        return $latestConsultation?->updated_at?->toDateString();
    }

    public function getConsultationSummaryTime(int $scientificWorkerId): ?string
    {
        $activeSemester = $this->getActiveConsultationSemesterUseCase->execute();

        if (!$activeSemester) {
            return null;
        }

        $totalDuration = 0;

        $semesterConsultations = SemesterConsultation::where('scientific_worker_id', $scientificWorkerId)
            ->where('semester_id', $activeSemester->id)
            ->whereNotIn('day', [WeekdayEnum::SATURDAY->value, WeekdayEnum::SUNDAY->value])
            ->get();

        foreach ($semesterConsultations as $consultation) {
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
        if ($type === ConsultationType::Semester) {
            return User::where('role', RoleEnum::SCIENTIFIC_WORKER)
                ->where(function ($query) use ($semesterId): void {
                    $query->whereHas('semesterConsultations', fn ($q) => $q->where('semester_id', $semesterId))
                        ->orWhereHas('partTimeConsultations', fn ($q) => $q->where('semester_id', $semesterId));
                })
                ->with([
                    'semesterConsultations' => fn ($q) => $q->where('semester_id', $semesterId)->orderBy('day')->orderBy('start_time'),
                    'partTimeConsultations' => fn ($q) => $q->where('semester_id', $semesterId)->orderBy('consultation_date')->orderBy('start_time'),
                ])
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        }

        $consultationRelation = match ($type) {
            ConsultationType::Session => 'sessionConsultations',
            ConsultationType::PartTime => 'partTimeConsultations',
            default => 'semesterConsultations',
        };

        return User::where('role', RoleEnum::SCIENTIFIC_WORKER)
            ->whereHas($consultationRelation, function ($query) use ($semesterId): void {
                $query->where('semester_id', $semesterId);
            })
            ->with([
                $consultationRelation => function ($query) use ($semesterId, $type): void {
                    $q = $query->where('semester_id', $semesterId);

                    if ($type === ConsultationType::Semester) {
                        $q->orderBy('day')->orderBy('start_time');
                    } else { // Session and PartTime
                        $q->orderBy('consultation_date')->orderBy('start_time');
                    }
                },
            ])
            ->orderBy('last_name')
            ->orderBy('first_name')
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
            ConsultationType::PartTime => 'partTimeConsultations',
        };

        return User::where('role', RoleEnum::SCIENTIFIC_WORKER)
            ->whereDoesntHave($consultationRelation, function ($query) use ($semesterId): void {
                $query->where('semester_id', $semesterId);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }
}
