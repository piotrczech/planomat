<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use Livewire\Component;

class NewSessionConsultationComponent extends Component
{
    public function mount(): void
    {

    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.new-session-consultation-component');
    }
}
