<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Dashboard;

use App\Domain\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ConsultationsCard extends Component
{
    public array $consultations = [];

    public function mount(): void
    {
    }

    public function redirectToForm()
    {
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return Auth::user()->role === RoleEnum::SCIENTIFIC_WORKER
            ? view('consultation::dashboard.scientific-worker.consultations-card')
            : view('consultation::dashboard.dean-office.consultations-card');
    }
}
