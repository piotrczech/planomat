<?php

declare(strict_types=1);

namespace App\Application\Semester\UseCases;

use App\Application\ActivityLog\UseCases\StoreActivityLogUseCase;
use App\Domain\Dto\StoreActivityLogDto;
use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;
use App\Enums\ActivityLogActionEnum;
use App\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;

final readonly class DeleteSemesterUseCase
{
    public function __construct(
        private SemesterRepositoryInterface $semesterRepository,
        private StoreActivityLogUseCase $storeActivityLogUseCase,
    ) {
    }

    public function execute(int $id): bool
    {
        $deleted = $this->semesterRepository->delete($id);

        if ($deleted) {
            $this->storeActivityLogUseCase->execute(
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
