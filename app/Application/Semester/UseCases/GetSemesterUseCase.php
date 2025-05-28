<?php

declare(strict_types=1);

namespace App\Application\Semester\UseCases;

use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;
use App\Models\Semester;

final readonly class GetSemesterUseCase
{
    public function __construct(private SemesterRepositoryInterface $semesterRepository)
    {
    }

    public function execute(int $id): ?Semester
    {
        return $this->semesterRepository->findById($id);
    }
}
