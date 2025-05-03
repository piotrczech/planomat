<?php

declare(strict_types=1);

namespace Modules\Consultation\Http\Livewire\Dashboard\Components;

use Livewire\Component;

class ConsultationItem extends Component
{
    public array $consultation = [];

    public function render()
    {
        return view('consultation::dashboard.consultation-item');
    }
}
