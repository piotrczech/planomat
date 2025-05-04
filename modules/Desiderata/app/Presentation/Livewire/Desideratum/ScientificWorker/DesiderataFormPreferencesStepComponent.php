<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Livewire\Desideratum\ScientificWorker;

use Spatie\LivewireWizard\Components\StepComponent;

class DesiderataFormPreferencesStepComponent extends StepComponent
{
    public function render()
    {
        return view('desiderata::components.desideratum.form-wizard.desiderata-form-preferences-step-component');
    }
}
