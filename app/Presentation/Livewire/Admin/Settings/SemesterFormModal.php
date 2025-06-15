<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\Semester\CreateSemesterUseCase;
use App\Application\UseCases\Semester\GetSemesterUseCase;
use App\Application\UseCases\Semester\UpdateSemesterUseCase;
use App\Domain\Dto\StoreSemesterDto;
use App\Domain\Dto\UpdateSemesterDto;
use App\Domain\Enums\SemesterSeasonEnum;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Throwable;

class SemesterFormModal extends Component
{
    public ?int $semesterId = null;

    public ?int $start_year = null;

    public ?string $season = null;

    public ?string $semester_start_date = null;

    public ?string $session_start_date = null;

    public ?string $end_date = null;

    public function mount(Application $app, ?int $semesterId = null): void
    {
        $this->semesterId = $semesterId;

        if ($this->semesterId) {
            $getSemesterUseCase = $app->make(GetSemesterUseCase::class);
            $semester = $getSemesterUseCase->execute($this->semesterId);

            if ($semester) {
                $this->start_year = (int) $semester->start_year;
                $this->season = (string) $semester->season->value;
                $this->semester_start_date = $semester->semester_start_date->format('Y-m-d');
                $this->session_start_date = $semester->session_start_date->format('Y-m-d');
                $this->end_date = $semester->end_date->format('Y-m-d');
            }
        }
    }

    public function saveSemester(Application $app): void
    {
        $dataToValidate = [
            'start_year' => $this->start_year,
            'season' => $this->season,
            'semester_start_date' => $this->semester_start_date,
            'session_start_date' => $this->session_start_date,
            'end_date' => $this->end_date,
        ];

        Log::debug('SemesterFormModal: Data before validation', $dataToValidate);

        try {
            if ($this->semesterId) {
                $dataToValidate['id'] = $this->semesterId;
                $rules = UpdateSemesterDto::rules($dataToValidate);
                $messages = UpdateSemesterDto::messages();

                $validatedData = Validator::make($dataToValidate, $rules, $messages)->validate();

                $dto = UpdateSemesterDto::from($validatedData);
                $updateSemesterUseCase = $app->make(UpdateSemesterUseCase::class);
                $updateSemesterUseCase->execute($this->semesterId, $dto);
            } else {
                $rules = StoreSemesterDto::rules($dataToValidate);
                $messages = StoreSemesterDto::messages();

                $validatedData = Validator::make($dataToValidate, $rules, $messages)->validate();

                $dto = StoreSemesterDto::from($validatedData);
                $createSemesterUseCase = $app->make(CreateSemesterUseCase::class);
                $createSemesterUseCase->execute($dto);
            }

            $this->dispatch('semesterSaved');
            $this->closeModal();
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
        } catch (Throwable $e) {
            session()->flash('error', 'Wystąpił nieoczekiwany błąd. Proszę spróbować ponownie.');
        }
    }

    public function closeModal(): void
    {
        $this->dispatch('closeSemesterFormModal');
    }

    public function render(): View
    {
        return view('livewire.admin.settings.semester-form-modal', [
            'seasons' => SemesterSeasonEnum::cases(),
        ]);
    }
}
