<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Settings;

use App\Application\Semester\UseCases\CreateSemesterUseCase;
use App\Application\Semester\UseCases\GetSemesterUseCase;
use App\Application\Semester\UseCases\UpdateSemesterUseCase;
use App\Domain\Semester\Dto\StoreSemesterDto;
use App\Domain\Semester\Dto\UpdateSemesterDto;
use App\Enums\SemesterSeasonEnum;
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
                $this->season = (string) $semester->season->value; // Poprawka: rzutowanie na string
                $this->semester_start_date = $semester->semester_start_date->format('Y-m-d');
                $this->session_start_date = $semester->session_start_date->format('Y-m-d');
                $this->end_date = $semester->end_date->format('Y-m-d');
            }
        }
        // Ustawienie domyślnego sezonu, jeśli to nowy semestr, np. zimowy
        // $this->season = SemesterSeasonEnum::WINTER->value;
        // Lub pozostawienie null i wymuszenie wyboru przez użytkownika

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
                Log::debug('SemesterFormModal: Validating for updating semester', ['data' => $dataToValidate]);
                $rules = UpdateSemesterDto::rules($dataToValidate);
                $messages = UpdateSemesterDto::messages();
                Log::debug('SemesterFormModal: Validation rules for update', $rules);

                $validatedData = Validator::make($dataToValidate, $rules, $messages)->validate();
                Log::debug('SemesterFormModal: Data AFTER validation (update)', $validatedData);

                $dto = UpdateSemesterDto::from($validatedData);
                $updateSemesterUseCase = $app->make(UpdateSemesterUseCase::class);
                $updateSemesterUseCase->execute($this->semesterId, $dto);
            } else {
                Log::debug('SemesterFormModal: Validating for new semester', ['data' => $dataToValidate]);
                $rules = StoreSemesterDto::rules($dataToValidate);
                $messages = StoreSemesterDto::messages();
                Log::debug('SemesterFormModal: Validation rules for new semester', $rules);

                $validatedData = Validator::make($dataToValidate, $rules, $messages)->validate();
                Log::debug('SemesterFormModal: Data AFTER validation (new semester)', $validatedData);

                $dto = StoreSemesterDto::from($validatedData);
                $createSemesterUseCase = $app->make(CreateSemesterUseCase::class);
                $createSemesterUseCase->execute($dto);
            }

            $this->dispatch('semesterSaved');
            $this->closeModal();
        } catch (ValidationException $e) {
            Log::error('SemesterFormModal: ValidationException caught', ['errors' => $e->errors(), 'data' => $dataToValidate]);
            $this->setErrorBag($e->validator->errors());
        } catch (Throwable $e) {
            Log::critical('CRITICAL ERROR IN SemesterFormModal::saveSemester: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'data' => $dataToValidate,
            ]);
            session()->flash('error', 'Wystąpił nieoczekiwany błąd. Proszę spróbować ponownie.'); // Można dodać wiadomość dla użytkownika
            // throw $e; // Możesz zdecydować, czy rzucać dalej, czy tylko logować i pokazać błąd użytkownikowi
        }
    }

    public function closeModal(): void
    {
        $this->dispatch('closeSemesterFormModal');
    }

    public function render(): View
    {
        return view('livewire.admin.settings.semester-form-modal', [
            'seasons' => SemesterSeasonEnum::cases(), // Przekazanie sezonów do widoku
        ]);
    }
}
