<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Livewire\Desideratum\ScientificWorker;

use Spatie\LivewireWizard\Components\WizardComponent;

class DesiderataFormWizardComponent extends WizardComponent
{
    public function steps(): array
    {
        return [
            DesiderataFormPreferencesStepComponent::class,
            DesiderataFormAvailabilityStepComponent::class,
        ];
    }
}
