<?php

declare(strict_types=1);

namespace App\Application\Semester\UseCases;

use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;

final readonly class DeleteSemesterUseCase
{
    public function __construct(private SemesterRepositoryInterface $semesterRepository)
    {
    }

    public function execute(int $id): bool
    {
        // Logika biznesowa specyficzna dla usuwania (np. zdarzenia) może być tutaj.
        return $this->semesterRepository->delete($id);
    }
}
