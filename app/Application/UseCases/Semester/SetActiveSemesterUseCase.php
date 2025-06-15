<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Application\UseCases\ActivityLog\CreateActivityLogUseCase;
use App\Domain\Interfaces\SemesterRepositoryInterface;

final readonly class SetActiveSemesterUseCase
{
    public function __construct(
        private SemesterRepositoryInterface $semesterRepository,
        private CreateActivityLogUseCase $createActivityLogUseCase,
    ) {
    }

    public function execute(int $semesterId): bool
    {
        $result = $this->semesterRepository->setActiveSemester($semesterId);

        return $result;
    }
}
