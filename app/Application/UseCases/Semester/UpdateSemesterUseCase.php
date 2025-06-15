<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Domain\Dto\UpdateSemesterDto;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Infrastructure\Models\Semester;

final readonly class UpdateSemesterUseCase
{
    public function __construct(private SemesterRepositoryInterface $semesterRepository)
    {
    }

    public function execute(int $id, UpdateSemesterDto $dto): ?Semester
    {
        return $this->semesterRepository->update($id, $dto);
    }
}
