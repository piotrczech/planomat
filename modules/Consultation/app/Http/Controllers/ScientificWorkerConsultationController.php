<?php

declare(strict_types=1);

namespace Modules\Consultation\Http\Controllers;

use App\Http\Controllers\Controller;

class ScientificWorkerConsultationController extends Controller
{
    public function index()
    {
        return redirect()->route('consultations.scientific-worker.my-semester-consultation');
    }

    public function semesterConsultationIndex()
    {
        return view('consultation::consultations.scientific-worker.my-semester-consultation');
    }

    public function sessionConsultationIndex()
    {
        return view('consultation::consultations.scientific-worker.my-session-consultation');
    }
}
