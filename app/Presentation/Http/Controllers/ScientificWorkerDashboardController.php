<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Infrastructure\Models\Semester;
use Illuminate\Contracts\View\View;

final class ScientificWorkerDashboardController extends Controller
{
    public function __invoke(): View
    {
        $currentSemester = Semester::getCurrentSemester();

        return view('dashboards.scientific-worker', [
            'currentSemester' => $currentSemester,
        ]);
    }
}
