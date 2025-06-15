<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Infrastructure\Models\Semester;

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
