<?php

declare(strict_types=1);

namespace App\Application\Semester\UseCases;

use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;

final readonly class GetCurrentSemesterDatesUseCase
{
    public function __construct(
        private SemesterRepositoryInterface $semesterRepository,
    ) {
    }

    /**
     * @return array{session_start_date: string, end_date: string}|null
     */
    public function execute(): ?array
    {
        $semester = $this->semesterRepository->findCurrentSemester();

        if ($semester) {
            return [
                'session_start_date' => $semester->session_start_date->toDateString(),
                'end_date' => $semester->end_date->toDateString(),
            ];
        }

        return null;
    }
}
