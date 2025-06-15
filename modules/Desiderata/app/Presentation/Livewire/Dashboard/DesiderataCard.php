<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Livewire\Dashboard;

use App\Domain\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DesiderataCard extends Component
{
    public array $desiderataItems = [];

    public function mount(): void
    {
    }

    public function render()
    {
        return Auth::user()->role === RoleEnum::SCIENTIFIC_WORKER
            ? view('desiderata::dashboard.scientific-worker.desiderata-card')
            : view('desiderata::dashboard.dean-office.desiderata-card');
    }
}
