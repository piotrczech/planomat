<?php

declare(strict_types=1);

namespace Modules\Desiderata\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;

class ScientificWorkerDesiderataController extends Controller
{
    /**
     * Display the desideratum form view
     */
    public function index(DesideratumRepositoryInterface $repository)
    {
        // Retrieve current semester ID (hardcoded for now, would come from a service in production)
        $currentSemesterId = 1;

        // Try to load existing desideratum data for the current user and semester
        $existingDesideratum = $repository->findByScientificWorkerAndSemester(
            Auth::id(),
            $currentSemesterId,
        );

        // Pass the data to the view (will be used by Livewire)
        return view('desiderata::desideratum.scientific-worker.my-desiderata-view', [
            'currentSemesterId' => $currentSemesterId,
            'existingDesideratum' => $existingDesideratum,
            'hasExistingDesiderata' => $existingDesideratum !== null,
        ]);
    }
}
