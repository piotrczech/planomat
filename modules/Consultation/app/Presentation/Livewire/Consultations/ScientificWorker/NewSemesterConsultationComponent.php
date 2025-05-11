<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use Livewire\Component;

class NewSemesterConsultationComponent extends Component
{
    public function mount(): void
    {
    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.new-semester-consultation-component');
    }
}
