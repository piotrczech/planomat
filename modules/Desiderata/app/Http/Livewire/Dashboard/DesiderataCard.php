<?php

declare(strict_types=1);

namespace Modules\Desiderata\Http\Livewire\Dashboard;

use Livewire\Component;

class DesiderataCard extends Component
{
    public int $remainingDays = 14;

    public bool $wasFormSubmitted = false;

    public function redirectToForm()
    {
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('desiderata::dashboard.desiderata-card');
    }
}
