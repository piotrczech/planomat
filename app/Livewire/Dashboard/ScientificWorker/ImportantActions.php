<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\ScientificWorker;

use App\Application\Dashboard\GetScientificWorkerActionsUseCase;
use App\Domain\Dashboard\Dto\ScientificWorkerActionStatusDto;
use Livewire\Component;

class ImportantActions extends Component
{
    public ScientificWorkerActionStatusDto $actions;

    public function mount(GetScientificWorkerActionsUseCase $getScientificWorkerActionsUseCase): void
    {
        $this->actions = $getScientificWorkerActionsUseCase->execute();
    }

    public function render()
    {
        return view('livewire.dashboard.scientific-worker.important-actions');
    }
}
