<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Application\UseCases\ActivityLog\CreateActivityLogUseCase;
use App\Domain\Dto\StoreActivityLogDto;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Domain\Enums\ActivityLogActionEnum;
use App\Domain\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;

final readonly class DeleteSemesterUseCase
{
    public function __construct(
        private SemesterRepositoryInterface $semesterRepository,
        private CreateActivityLogUseCase $createActivityLogUseCase,
    ) {
    }

    public function execute(int $id): bool
    {
        $deleted = $this->semesterRepository->delete($id);

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
