<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Domain\Interfaces\SemesterRepositoryInterface;

final readonly class GetCurrentSemesterDatesUseCase
{
    public function __construct(
        private SemesterRepositoryInterface $semesterRepository,
    ) {
    }

    /**
     * @return array{semester_start_date: string, session_start_date: string, end_date: string}|null
     */
    public function execute(): ?array
    {
        $semester = $this->semesterRepository->findCurrentSemester();

        if ($semester) {
            return [
                'semester_start_date' => $semester->semester_start_date->toDateString(),
                'session_start_date' => $semester->session_start_date->toDateString(),
                'end_date' => $semester->end_date->toDateString(),
            ];
        }

        return null;
    }
}
