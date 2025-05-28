<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Admin\Settings;

use App\Application\Semester\UseCases\CreateSemesterUseCase;
use App\Application\Semester\UseCases\GetSemesterUseCase;
use App\Application\Semester\UseCases\UpdateSemesterUseCase;
use App\Domain\Semester\Dto\StoreSemesterDto;
use App\Domain\Semester\Dto\UpdateSemesterDto;
use App\Enums\SemesterSeasonEnum;
use App\Livewire\Admin\Settings\SemesterFormModal; // Poprawiona ścieżka importu
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;

class SemesterFormModalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_successfully_for_creating_new_semester(): void
    {
        Livewire::test(SemesterFormModal::class)
            ->assertSee('Semester Form');
    }

    #[Test]
    public function it_renders_successfully_and_loads_data_for_editing_semester(): void
    {
        // Tworzenie obiektu Semester manualnie zamiast przez fabrykę
        $semesterId = 1;
        $semesterData = [
            'id' => $semesterId,
            'start_year' => 2023,
            'season' => SemesterSeasonEnum::WINTER, // Przekazujemy enum bezpośrednio
            'semester_start_date' => '2023-10-01',
            'session_start_date' => '2024-01-15',
            'end_date' => '2024-02-28',
        ];
        $semester = new Semester($semesterData);
        $semester->id = $semesterId; // Ustawienie ID, bo nie ma zapisu do bazy
        // Ręczne ustawienie castowanych atrybutów Carbon, jeśli model ich oczekuje (w tym przypadku nie jest to krytyczne dla logiki testu, bo repo zwraca ten obiekt)
        $semester->semester_start_date = \Carbon\Carbon::parse($semesterData['semester_start_date']);
        $semester->session_start_date = \Carbon\Carbon::parse($semesterData['session_start_date']);
        $semester->end_date = \Carbon\Carbon::parse($semesterData['end_date']);

        // Mock SemesterRepositoryInterface
        $semesterRepositoryMock = Mockery::mock(SemesterRepositoryInterface::class);
        $semesterRepositoryMock->shouldReceive('findById')->with($semester->id)->once()->andReturn($semester);

        $this->app->when(GetSemesterUseCase::class)
            ->needs(SemesterRepositoryInterface::class)
            ->give(fn () => $semesterRepositoryMock);

        Livewire::test(SemesterFormModal::class, ['semesterId' => $semester->id])
            ->assertSee('Semester Form')
            ->assertSet('start_year', $semesterData['start_year'])
            ->assertSet('season', (string) $semesterData['season']->value)
            ->assertSet('semester_start_date', $semesterData['semester_start_date'])
            ->assertSet('session_start_date', $semesterData['session_start_date'])
            ->assertSet('end_date', $semesterData['end_date']);
    }

    #[Test]
    public function it_creates_new_semester_successfully(): void
    {
        $semesterRepositoryMock = Mockery::mock(SemesterRepositoryInterface::class);
        $this->app->when(CreateSemesterUseCase::class)
            ->needs(SemesterRepositoryInterface::class)
            ->give(fn () => $semesterRepositoryMock);

        $semesterData = [
            'start_year' => 2025,
            'season' => SemesterSeasonEnum::SPRING->value,
            'semester_start_date' => '2025-03-01',
            'session_start_date' => '2025-06-15',
            'end_date' => '2025-09-30',
        ];

        // Oczekujemy, że metoda create na repozytorium zostanie wywołana z odpowiednim DTO
        $semesterRepositoryMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (StoreSemesterDto $dto) use ($semesterData) {
                return $dto->start_year === $semesterData['start_year'] &&
                       $dto->season === (string) $semesterData['season'] &&
                       $dto->semester_start_date === $semesterData['semester_start_date'] &&
                       $dto->session_start_date === $semesterData['session_start_date'] &&
                       $dto->end_date === $semesterData['end_date'];
            }))
            ->andReturn(new Semester($semesterData)); // Repozytorium zwraca nowo utworzony model

        Livewire::test(SemesterFormModal::class)
            ->set('start_year', $semesterData['start_year'])
            ->set('season', $semesterData['season'])
            ->set('semester_start_date', $semesterData['semester_start_date'])
            ->set('session_start_date', $semesterData['session_start_date'])
            ->set('end_date', $semesterData['end_date'])
            ->call('saveSemester')
            ->assertEmitted('semesterSaved')
            ->assertEmitted('closeSemesterFormModal');
    }

    #[Test]
    public function it_updates_existing_semester_successfully(): void
    {
        $semester = Semester::factory()->create();

        $semesterRepositoryMock = Mockery::mock(SemesterRepositoryInterface::class);

        // Mock dla GetSemesterUseCase (używanego w mount)
        $semesterRepositoryMock->shouldReceive('findById')->with($semester->id)->once()->andReturn($semester);

        // Mock dla UpdateSemesterUseCase
        $updateData = [
            'id' => $semester->id,
            'start_year' => 2026,
            'season' => SemesterSeasonEnum::WINTER->value,
            'semester_start_date' => '2026-10-01',
            'session_start_date' => '2027-01-15',
            'end_date' => '2027-02-28',
        ];

        $semesterRepositoryMock->shouldReceive('update')
            ->once()
            ->with($semester->id, Mockery::on(function (UpdateSemesterDto $dto) use ($updateData) {
                return $dto->id === $updateData['id'] &&
                       $dto->start_year === $updateData['start_year'] &&
                       $dto->season === (string) $updateData['season'] &&
                       $dto->semester_start_date === $updateData['semester_start_date'] &&
                       $dto->session_start_date === $updateData['session_start_date'] &&
                       $dto->end_date === $updateData['end_date'];
            }))
            ->andReturn(new Semester($updateData)); // Repozytorium zwraca zaktualizowany model

        // Powiedz kontenerowi, aby użył naszego mocka, gdy UseCase'y potrzebują SemesterRepositoryInterface
        $this->app->when(GetSemesterUseCase::class)
            ->needs(SemesterRepositoryInterface::class)
            ->give(fn () => $semesterRepositoryMock);
        $this->app->when(UpdateSemesterUseCase::class)
            ->needs(SemesterRepositoryInterface::class)
            ->give(fn () => $semesterRepositoryMock);

        Livewire::test(SemesterFormModal::class, ['semesterId' => $semester->id])
            ->set('start_year', $updateData['start_year'])
            ->set('season', $updateData['season'])
            ->set('semester_start_date', $updateData['semester_start_date'])
            ->set('session_start_date', $updateData['session_start_date'])
            ->set('end_date', $updateData['end_date'])
            ->call('saveSemester')
            ->assertEmitted('semesterSaved')
            ->assertEmitted('closeSemesterFormModal');
    }

    #[Test]
    public function it_shows_validation_errors_for_invalid_data_on_create(): void
    {
        Livewire::test(SemesterFormModal::class)
            ->set('start_year', 2025)
            ->set('season', SemesterSeasonEnum::SPRING->value)
            ->set('semester_start_date', 'invalid-date') // Niepoprawny format daty
            ->set('session_start_date', '2025-06-15')
            ->set('end_date', '2025-09-30')
            ->call('saveSemester')
            ->assertHasErrors(['semester_start_date']);
    }

    #[Test]
    public function it_shows_validation_errors_for_invalid_data_on_update(): void
    {
        $semesterId = 1;
        // Minimalne dane dla istniejacego semestru potrzebne do mount
        $existingSemester = new Semester(['id' => $semesterId, 'start_year' => 2023, 'season' => SemesterSeasonEnum::WINTER, 'semester_start_date' => '2023-10-01', 'session_start_date' => '2023-11-01', 'end_date' => '2023-12-01']);
        $existingSemester->id = $semesterId;
        $existingSemester->semester_start_date = \Carbon\Carbon::parse('2023-10-01');
        $existingSemester->session_start_date = \Carbon\Carbon::parse('2023-11-01');
        $existingSemester->end_date = \Carbon\Carbon::parse('2023-12-01');

        $semesterRepositoryMock = Mockery::mock(SemesterRepositoryInterface::class);
        $semesterRepositoryMock->shouldReceive('findById')->with($semesterId)->once()->andReturn($existingSemester);
        $this->app->when(GetSemesterUseCase::class)
            ->needs(SemesterRepositoryInterface::class)
            ->give(fn () => $semesterRepositoryMock);

        Livewire::test(SemesterFormModal::class, ['semesterId' => $semesterId])
            ->set('semester_start_date', 'invalid-date')
            ->call('saveSemester')
            ->assertHasErrors(['semester_start_date']);
    }

    #[Test]
    public function it_emits_close_modal_event_when_close_modal_is_called(): void
    {
        Livewire::test(SemesterFormModal::class)
            ->call('closeModal')
            ->assertEmitted('closeSemesterFormModal');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
