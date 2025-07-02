<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Livewire\Dashboard;

use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use App\Domain\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DesiderataCard extends Component
{
    public $semester;

    public function mount(GetActiveDesiderataSemesterUseCase $useCase): void
    {
        $this->semester = $useCase->execute();
    }

    public function render()
    {
        return Auth::user()->role === RoleEnum::SCIENTIFIC_WORKER
            ? view('desiderata::dashboard.scientific-worker.desiderata-card')
            : view('desiderata::dashboard.dean-office.desiderata-card');
    }
}
