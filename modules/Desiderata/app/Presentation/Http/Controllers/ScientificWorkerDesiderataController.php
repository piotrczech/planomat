<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Application\UseCases\ScientificWorker\GetLastUpdateDateForDesideratumUseCase;

class ScientificWorkerDesiderataController extends Controller
{
    public function index(
        DesideratumRepositoryInterface $repository,
        GetLastUpdateDateForDesideratumUseCase $getLastUpdatedDate,
    ) {
        $currentSemesterId = 1;

        $existingDesideratum = $repository->findByScientificWorkerAndSemester(
            Auth::id(),
            $currentSemesterId,
        );

        $lastUpdateDate = $getLastUpdatedDate->execute();

        return view('desiderata::desideratum.scientific-worker.my-desiderata-view', [
            'currentSemesterId' => $currentSemesterId,
            'existingDesideratum' => $existingDesideratum,
            'hasExistingDesiderata' => $existingDesideratum !== null,
            'lastUpdateDate' => $lastUpdateDate,
        ]);
    }
}
