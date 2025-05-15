<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Livewire\Desideratum\ScientificWorker;

use Illuminate\Support\Facades\Auth;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Spatie\LivewireWizard\Components\StepComponent;

class DesiderataFormPreferencesStepComponent extends StepComponent
{
    public bool $wantStationary = false;

    public bool $wantNonStationary = false;

    public bool $agreeToOvertime = false;

    public array $proficientCourseIds = [];

    public array $wantedCourseIds = [];

    public array $unwantedCourseIds = [];

    public int $masterThesesCount;

    public int $bachelorThesesCount;

    public int $maxHoursPerDay;

    public int $maxConsecutiveHours;

    public function mount(DesideratumRepositoryInterface $repository): void
    {
        $currentUserId = Auth::id();
        $semesterId = 1;

        $existingDesideratum = $repository->findByScientificWorkerAndSemester($currentUserId, $semesterId);

        if ($existingDesideratum) {
            $this->wantStationary = $existingDesideratum->wantStationary;
            $this->wantNonStationary = $existingDesideratum->wantNonStationary;
            $this->agreeToOvertime = $existingDesideratum->agreeToOvertime;
            $this->proficientCourseIds = $existingDesideratum->proficientCourseIds;
            $this->wantedCourseIds = $existingDesideratum->wantedCourseIds;
            $this->unwantedCourseIds = $existingDesideratum->unwantedCourseIds;
            $this->masterThesesCount = $existingDesideratum->masterThesesCount;
            $this->bachelorThesesCount = $existingDesideratum->bachelorThesesCount;
            $this->maxHoursPerDay = $existingDesideratum->maxHoursPerDay;
            $this->maxConsecutiveHours = $existingDesideratum->maxConsecutiveHours;
        }
    }

    public function render()
    {
        return view('desiderata::components.desideratum.form-wizard.desiderata-form-preferences-step-component');
    }
}
