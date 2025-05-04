<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Livewire\Dashboard;

use Livewire\Component;

class DesiderataCard extends Component
{
    public int $remainingDays = 14;

    public bool $wasFormSubmitted = false;

    public function redirectToForm()
    {
        return redirect()->route('desiderata.scientific-worker.my-desiderata');
    }

    public function render()
    {
        return view('desiderata::dashboard.desiderata-card');
    }
}
