<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Livewire\Desideratum\ScientificWorker;

use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use App\Application\UseCases\TimeSlot\GetAllTimeSlotsUseCase;
use App\Domain\Enums\WeekdayEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Desiderata\Application\UseCases\ScientificWorker\UpdateOrCreateDesideratumUseCase;
use Modules\Desiderata\Domain\Dto\DesiderataFormAvailabilityDto;
use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Spatie\LivewireWizard\Components\StepComponent;
use Exception;

class DesiderataFormAvailabilityStepComponent extends StepComponent
{
    public array $unavailableTimeSlots = [];

    public int $selectedSlotsCount = 0;

    public int $maxUnavailableSlots = 5;

    public string $additionalNotes = '';

    public $errorMessage = null;

    public function mount(DesideratumRepositoryInterface $repository): void
    {
        $wizardState = $this->state()->forStepClass(self::class);

        $this->initTimeSlots();

        if (!empty($wizardState) && isset($wizardState['selectedSlotsCount']) && $wizardState['selectedSlotsCount'] > 0) {
            $wizardSlots = $wizardState['unavailableTimeSlots'] ?? [];
            $hasValidWizardSlots = false;

            foreach ($wizardSlots as $day => $slots) {
                foreach ($slots as $slot) {
                    if (isset($slot['selected']) && $slot['selected']) {
                        $hasValidWizardSlots = true;

                        break 2;
                    }
                }
            }

            if ($hasValidWizardSlots) {
                $this->unavailableTimeSlots = $wizardSlots;
                $this->selectedSlotsCount = $wizardState['selectedSlotsCount'];
                $this->additionalNotes = $wizardState['additionalNotes'] ?? '';
            } else {
                $this->loadExistingDataFromDatabase($repository);
            }
        } else {
            $this->loadExistingDataFromDatabase($repository);
        }
    }

    public function render()
    {
        return view('desiderata::components.desideratum.form-wizard.desiderata-form-availability-step-component');
    }

    private function initTimeSlots(): void
    {
        $timeSlots = app(GetAllTimeSlotsUseCase::class)
            ->execute()
            ->map(fn ($slot) => [
                'id' => $slot->id,
                'range' => $slot->formatted_time_range,
            ]);

        foreach (WeekdayEnum::cases() as $day) {
            $this->unavailableTimeSlots[$day->value] = [];

            foreach ($timeSlots as $slot) {
                $this->unavailableTimeSlots[$day->value][$slot['id']] = [
                    'selected' => false,
                    'id' => $slot['id'],
                    'range' => $slot['range'],
                ];
            }
        }
    }

    private function loadExistingDataFromDatabase(DesideratumRepositoryInterface $repository): void
    {
        $currentUserId = Auth::id();
        $semesterId = app(GetActiveDesiderataSemesterUseCase::class)->execute()->id;

        $existingDesideratum = $repository->findByScientificWorkerAndSemester($currentUserId, $semesterId);

        if (!$existingDesideratum) {
            $existingDesideratum = $repository->findLatestByScientificWorkerBeforeSemester(
                $currentUserId,
                $semesterId,
            );
        }

        if ($existingDesideratum) {
            $this->additionalNotes = $existingDesideratum->additionalNotes ?? '';

            if (!empty($existingDesideratum->unavailableTimeSlots)) {
                $this->selectedSlotsCount = 0;

                foreach ($existingDesideratum->unavailableTimeSlots as $day => $slotIds) {
                    if (!isset($this->unavailableTimeSlots[$day]) || !is_array($slotIds)) {
                        continue;
                    }

                    foreach ($slotIds as $slotId) {
                        if (isset($this->unavailableTimeSlots[$day][$slotId])) {
                            $this->unavailableTimeSlots[$day][$slotId]['selected'] = true;
                            $this->selectedSlotsCount++;
                        }
                    }
                }
            }
        }
    }

    public function toggleTimeSlot(string $day, int $slotId): void
    {
        $isCurrentlySelected = $this->unavailableTimeSlots[$day][$slotId]['selected'];

        if ($isCurrentlySelected) {
            $this->unavailableTimeSlots[$day][$slotId]['selected'] = false;
            $this->selectedSlotsCount--;
        } elseif ($this->selectedSlotsCount < $this->maxUnavailableSlots) {
            $this->unavailableTimeSlots[$day][$slotId]['selected'] = true;
            $this->selectedSlotsCount++;
        }
    }

    public function saveDesideratum(UpdateOrCreateDesideratumUseCase $useCase): void
    {
        try {
            $this->validate(DesiderataFormAvailabilityDto::rules(), DesiderataFormAvailabilityDto::messages());

            $preferencesState = $this->state()->forStepClass(DesiderataFormPreferencesStepComponent::class);
            $availabilityState = $this->state()->forStepClass(DesiderataFormAvailabilityStepComponent::class);

            $unavailableTimeSlots = array_map(
                fn (array $dayTimeSlots) => array_column(
                    array_filter(
                        $dayTimeSlots,
                        fn (array $slot) => $slot['selected'] ?? false,
                    ),
                    'id',
                ),
                $availabilityState['unavailableTimeSlots'],
            );

            $desideratumDto = UpdateOrCreateDesideratumDto::from([
                ...$preferencesState,
                'unavailableTimeSlots' => $unavailableTimeSlots,
                'additionalNotes' => $availabilityState['additionalNotes'],
            ]);

            $useCase->execute($desideratumDto);
            $this->dispatch('desideratumSaved');
            $this->previousStep();
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    protected function rules(): array
    {
        return DesiderataFormAvailabilityDto::rules();
    }

    protected function messages(): array
    {
        return DesiderataFormAvailabilityDto::messages();
    }
}
