<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetLastUpdateDateForSemesterConsultationUseCase;
use Modules\Consultation\Application\UseCases\ScientificWorker\GetLastUpdateDateForSessionConsultationUseCase;

class ScientificWorkerConsultationController extends Controller
{
    public function index()
    {
        return redirect()->route('consultations.scientific-worker.my-semester-consultation');
    }

    public function semesterConsultationIndex(
        GetLastUpdateDateForSemesterConsultationUseCase $getLastUpdatedDate,
    ) {
        $lastUpdateDate = $getLastUpdatedDate->execute();

        return view('consultation::consultations.scientific-worker.my-semester-consultation', [
            'lastUpdateDate' => $lastUpdateDate,
        ]);
    }

    public function sessionConsultationIndex(
        GetLastUpdateDateForSessionConsultationUseCase $getLastUpdatedDate,
    ) {
        $lastUpdateDate = $getLastUpdatedDate->execute();

        return view('consultation::consultations.scientific-worker.my-session-consultation', [
            'lastUpdateDate' => $lastUpdateDate,
        ]);
    }
}
