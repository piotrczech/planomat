<?php

declare(strict_types=1);

namespace Modules\Desiderata\Tests\Feature;

use App\Domain\Enums\SemesterSeasonEnum;
use App\Infrastructure\Models\Semester;
use App\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Infrastructure\Models\Desideratum;
use Tests\TestCase;

class DesiderataExportAccountStatusFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_unfilled_desiderata_excludes_suspended_and_archived_accounts(): void
    {
        $semester = $this->createSemester();

        $activeWithoutDesiderata = User::factory()->scientificWorker()->create();
        $suspendedWithoutDesiderata = User::factory()->scientificWorker()->create(['is_active' => false]);
        $archivedWithoutDesiderata = User::factory()->scientificWorker()->create();
        $archivedWithoutDesiderata->delete();

        $activeWithDesiderata = User::factory()->scientificWorker()->create();
        $this->createDesideratum($activeWithDesiderata, $semester);

        $repository = app(DesideratumRepositoryInterface::class);
        $result = $repository->getScientificWorkersWithoutDesiderata($semester->id);
        $resultIds = $result->modelKeys();

        $this->assertContains($activeWithoutDesiderata->id, $resultIds);
        $this->assertNotContains($suspendedWithoutDesiderata->id, $resultIds);
        $this->assertNotContains($archivedWithoutDesiderata->id, $resultIds);
        $this->assertNotContains($activeWithDesiderata->id, $resultIds);
    }

    public function test_full_desiderata_export_includes_suspended_and_archived_only_when_submitted(): void
    {
        $semester = $this->createSemester();

        $suspendedWithoutDesiderata = User::factory()->scientificWorker()->create(['is_active' => false]);

        $archivedWithDesiderata = User::factory()->scientificWorker()->create();
        $archivedWithDesiderata->delete();
        $this->createDesideratum($archivedWithDesiderata, $semester);

        $archivedWithoutDesiderata = User::factory()->scientificWorker()->create();
        $archivedWithoutDesiderata->delete();

        $repository = app(DesideratumRepositoryInterface::class);
        $result = $repository->getAllDesiderataForPdfExport($semester->id);
        $resultIds = $result->modelKeys();

        $this->assertContains($suspendedWithoutDesiderata->id, $resultIds);
        $this->assertContains($archivedWithDesiderata->id, $resultIds);
        $this->assertNotContains($archivedWithoutDesiderata->id, $resultIds);
    }

    public function test_chunked_desiderata_export_returns_same_workers_as_standard_export(): void
    {
        $semester = $this->createSemester();

        User::factory()->scientificWorker()->create();

        $suspendedWithoutDesiderata = User::factory()->scientificWorker()->create(['is_active' => false]);

        $archivedWithDesiderata = User::factory()->scientificWorker()->create();
        $archivedWithDesiderata->delete();
        $this->createDesideratum($archivedWithDesiderata, $semester);

        $archivedWithoutDesiderata = User::factory()->scientificWorker()->create();
        $archivedWithoutDesiderata->delete();

        $repository = app(DesideratumRepositoryInterface::class);

        $standardIds = $repository->getAllDesiderataForPdfExport($semester->id)->modelKeys();

        $chunkedWorkers = collect();
        $repository->getDesiderataForPdfExportChunked(
            semesterId: $semester->id,
            chunkSize: 2,
            callback: function ($workers) use ($chunkedWorkers): void {
                $chunkedWorkers->push(...$workers);
            },
        );
        $chunkedIds = $chunkedWorkers->pluck('id')->all();

        sort($standardIds);
        sort($chunkedIds);

        $this->assertSame($standardIds, $chunkedIds);
        $this->assertContains($suspendedWithoutDesiderata->id, $standardIds);
        $this->assertContains($archivedWithDesiderata->id, $standardIds);
        $this->assertNotContains($archivedWithoutDesiderata->id, $standardIds);
    }

    private function createDesideratum(User $user, Semester $semester): void
    {
        Desideratum::create([
            'semester_id' => $semester->id,
            'scientific_worker_id' => $user->id,
            'want_stationary' => true,
            'want_non_stationary' => false,
            'agree_to_overtime' => false,
            'master_theses_count' => 0,
            'bachelor_theses_count' => 0,
            'max_hours_per_day' => 6,
            'max_consecutive_hours' => 4,
            'additional_notes' => null,
        ]);
    }

    private function createSemester(): Semester
    {
        return Semester::create([
            'start_year' => 2025,
            'season' => SemesterSeasonEnum::WINTER,
            'semester_start_date' => '2025-10-01',
            'session_start_date' => '2026-02-01',
            'end_date' => '2026-02-28',
            'is_active' => false,
        ]);
    }
}
