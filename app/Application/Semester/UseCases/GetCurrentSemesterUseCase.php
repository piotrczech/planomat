<?php

declare(strict_types=1);

namespace App\Application\Semester\UseCases;

use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;
use App\Models\Semester;

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
