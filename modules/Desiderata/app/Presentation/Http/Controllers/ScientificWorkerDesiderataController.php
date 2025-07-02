<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Http\Controllers;

use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use App\Presentation\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Application\UseCases\ScientificWorker\GetLastUpdateDateForDesideratumUseCase;

class ScientificWorkerDesiderataController extends Controller
{
    public function index(
        DesideratumRepositoryInterface $repository,
        GetLastUpdateDateForDesideratumUseCase $getLastUpdatedDate,
        GetActiveDesiderataSemesterUseCase $getActiveDesideratumSemesterUseCase,
    ) {
        $currentSemester = $getActiveDesideratumSemesterUseCase->execute();

        if (!$currentSemester) {
            abort(404, 'No active semester found.');
        }

        $existingDesideratum = $repository->findByScientificWorkerAndSemester(
            Auth::id(),
            $currentSemester->id,
        );

        $hasExistingDesiderata = $existingDesideratum !== null;

        $defaultPreviousDesideratum = null;

        if (!$hasExistingDesiderata) {
            $defaultPreviousDesideratum = $repository->findLatestByScientificWorkerBeforeSemester(
                Auth::id(),
                $currentSemester->id,
            );
        }

        $lastUpdateDate = $getLastUpdatedDate->execute();

        return view('desiderata::desideratum.scientific-worker.my-desiderata-view', [
            'currentSemesterId' => $currentSemester->id,
            'currentSemester' => $currentSemester,
            'existingDesideratum' => $existingDesideratum,
            'hasExistingDesiderata' => $hasExistingDesiderata,
            'hasDefaultPrevDesiderata' => $defaultPreviousDesideratum !== null,
            'lastUpdateDate' => $lastUpdateDate,
        ]);
    }
}
