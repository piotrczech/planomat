<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Http\Controllers;

use App\Application\Semester\UseCases\GetCurrentSemesterUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Application\UseCases\ScientificWorker\GetLastUpdateDateForDesideratumUseCase;

class ScientificWorkerDesiderataController extends Controller
{
    public function index(
        DesideratumRepositoryInterface $repository,
        GetLastUpdateDateForDesideratumUseCase $getLastUpdatedDate,
        GetCurrentSemesterUseCase $getCurrentSemesterUseCase,
    ) {
        $currentSemester = $getCurrentSemesterUseCase->execute();

        if (!$currentSemester) {
            // Można tu zwrócić widok z błędem lub powiadomieniem
            abort(404, 'No active semester found.');
        }

        $existingDesideratum = $repository->findByScientificWorkerAndSemester(
            Auth::id(),
            $currentSemester->id,
        );

        $lastUpdateDate = $getLastUpdatedDate->execute();

        return view('desiderata::desideratum.scientific-worker.my-desiderata-view', [
            'currentSemesterId' => $currentSemester->id,
            'existingDesideratum' => $existingDesideratum,
            'hasExistingDesiderata' => $existingDesideratum !== null,
            'lastUpdateDate' => $lastUpdateDate,
        ]);
    }
}
