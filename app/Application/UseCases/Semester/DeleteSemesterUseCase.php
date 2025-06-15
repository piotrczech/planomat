<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Application\UseCases\ActivityLog\CreateActivityLogUseCase;
use App\Domain\Dto\StoreActivityLogDto;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Domain\Enums\ActivityLogActionEnum;
use App\Domain\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final readonly class DeleteSemesterUseCase
{
    public function __construct(
        private SemesterRepositoryInterface $semesterRepository,
        private CreateActivityLogUseCase $createActivityLogUseCase,
    ) {
    }

    public function execute(int $id): bool
    {
        $deleted = DB::transaction(function () use ($id): bool {
            $semester = $this->semesterRepository->findById($id);

            if (!$semester) {
                return false;
            }

            $semester->consultationSemesters()->delete();
            $semester->consultationSessions()->delete();
            $semester->desiderata()->delete();

            return $this->semesterRepository->delete($id);
        });

        if ($deleted) {
            $this->createActivityLogUseCase->execute(
                new StoreActivityLogDto(
                    userId: (string) Auth::id(),
                    module: ActivityLogModuleEnum::SEMESTER->value,
                    action: ActivityLogActionEnum::DELETE->value,
                ),
            );
        }

        return $deleted;
    }
}
