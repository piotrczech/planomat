<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Livewire\Consultations\ScientificWorker;

use App\Application\UseCases\Semester\GetCurrentSemesterDatesUseCase;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Consultation\Domain\Dto\CreateNewPartTimeConsultationDto;
use Exception;
use Illuminate\Support\Facades\App;
use Modules\Consultation\Application\UseCases\ScientificWorker\CreateNewPartTimeConsultationUseCase;

class NewPartTimeConsultationComponent extends Component
{
    public string $consultationDate = '';

    public string $consultationStartTime = '';

    public string $consultationEndTime = '';

    public string $consultationLocationBuilding = '';

    public ?string $consultationLocationRoom = null;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public string $startSemesterDate = '';

    public string $startSessionDate = '';

    protected function rules(): array
    {
        return [
            'consultationDate' => [
                'required',
                'date',
                Rule::unique('part_time_consultations', 'consultation_date')->where(function ($query) {
                    return $query->where('scientific_worker_id', auth()->id())
                        ->where(function ($q): void {
                            $q->where(function ($sub): void {
                                $sub->where('start_time', '<', $this->consultationEndTime)
                                    ->where('end_time', '>', $this->consultationStartTime);
                            });
                        });
                }),
            ],
            'consultationStartTime' => 'required|date_format:H:i',
            'consultationEndTime' => 'required|date_format:H:i|after:consultationStartTime',
            'consultationLocationBuilding' => 'required|string|max:255',
            'consultationLocationRoom' => 'nullable|string|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'consultationDate.unique' => __('consultation::consultation.timeslot_taken'),
            'consultationEndTime.after' => __('consultation::consultation.end_time_after_start_time'),
        ];
    }

    public function fetchPartTimeDates(): void
    {
        $useCase = App::make(GetCurrentSemesterDatesUseCase::class);
        $dates = $useCase->execute();

        if ($dates) {
            $this->startSemesterDate = $dates['semester_start_date'];
            $this->startSessionDate = $dates['session_start_date'];
        } else {
            $this->startSemesterDate = '';
            $this->startSessionDate = '';
            $this->errorMessage = __('consultation::consultation.No current semester found.');
        }
    }

    public function mount(): void
    {
        $this->resetForm();
        $this->fetchPartTimeDates();
    }

    public function render()
    {
        return view('consultation::components.consultations.scientific-worker.new-part-time-consultation-component');
    }

    public function addConsultation(): void
    {
        $validatedData = $this->validate();

        try {
            $requestData = CreateNewPartTimeConsultationDto::from($validatedData);

            $useCase = App::make(CreateNewPartTimeConsultationUseCase::class);
            $useCase->execute($requestData);

            $this->successMessage = __('consultation::consultation.Successfully created part-time consultation');
            $this->dispatch('consultationSaved');
            $this->resetForm();
        } catch (Exception $e) {
            $this->errorMessage = __('consultation::consultation.Error: :message', ['message' => $e->getMessage()]);
        }
    }

    public function resetForm(): void
    {
        $this->consultationDate = '';
        $this->consultationStartTime = '';
        $this->consultationEndTime = '';
        $this->consultationLocationBuilding = '';
        $this->consultationLocationRoom = null;
    }
}
