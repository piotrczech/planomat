<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\ScientificWorker;

use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Illuminate\Support\Facades\Auth;

final class GetLastUpdateDateForDesideratumUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
        private readonly GetActiveDesiderataSemesterUseCase $getActiveDesiderataSemesterUseCase,
    ) {
    }

    public function execute(): ?string
    {
        $scientificWorkerId = Auth::id();

        if (!$scientificWorkerId) {
            return null;
        }

        $activeSemester = $this->getActiveDesiderataSemesterUseCase->execute();

        if (!$activeSemester) {
            return null;
        }

        return $this->desideratumRepository->getLastUpdateDate(
            $scientificWorkerId,
            $activeSemester->id,
        );
    }
}
