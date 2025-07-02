<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use Illuminate\Contracts\View\View;

final class ScientificWorkerDashboardController extends Controller
{
    public function __invoke(
        GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
        GetActiveDesiderataSemesterUseCase $getActiveDesiderataSemesterUseCase,
    ): View {
        $consultationSemester = $getActiveConsultationSemesterUseCase->execute();
        $desiderataSemester = $getActiveDesiderataSemesterUseCase->execute();

        return view('dashboards.scientific-worker', [
            'consultationSemester' => $consultationSemester,
            'desiderataSemester' => $desiderataSemester,
        ]);
    }
}
