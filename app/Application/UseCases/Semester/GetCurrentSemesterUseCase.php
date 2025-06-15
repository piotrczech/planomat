<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Infrastructure\Models\Semester;

final class GetCurrentSemesterUseCase
{
    public function __construct(
        private readonly SemesterRepositoryInterface $semesterRepository,
    ) {
    }

    public function execute(): ?Semester
    {
        return $this->semesterRepository->findCurrentSemester();
    }
}
