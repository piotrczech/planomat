<?php

declare(strict_types=1);

namespace Modules\Desiderata\Http\Controllers;

use App\Http\Controllers\Controller;

class ScientificWorkerDesiderataController extends Controller
{
    public function index()
    {
        return view('desiderata::desideratum.scientific-worker.my-desiderata-view');
    }
}
