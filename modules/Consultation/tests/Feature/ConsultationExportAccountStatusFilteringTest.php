<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Feature;

use App\Domain\Enums\SemesterSeasonEnum;
use App\Infrastructure\Models\Semester;
use App\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Consultation\Domain\Enums\ConsultationType;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Infrastructure\Models\SessionConsultation;
use Tests\TestCase;

class ConsultationExportAccountStatusFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_unfilled_consultations_excludes_suspended_and_archived_accounts(): void
    {
        $semester = $this->createSemester();

        $activeWithoutConsultations = User::factory()->scientificWorker()->create();
        $suspendedWithoutConsultations = User::factory()->scientificWorker()->create(['is_active' => false]);
        $archivedWithoutConsultations = User::factory()->scientificWorker()->create();
        $archivedWithoutConsultations->delete();

        $activeWithConsultations = User::factory()->scientificWorker()->create();
        SessionConsultation::create([
            'scientific_worker_id' => $activeWithConsultations->id,
            'semester_id' => $semester->id,
            'consultation_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'location_building' => 'A1',
            'location_room' => '101',
        ]);

        $repository = app(ConsultationRepositoryInterface::class);
        $result = $repository->getScientificWorkersWithoutConsultations($semester->id, ConsultationType::Session);
        $resultIds = $result->modelKeys();

        $this->assertContains($activeWithoutConsultations->id, $resultIds);
        $this->assertNotContains($suspendedWithoutConsultations->id, $resultIds);
        $this->assertNotContains($archivedWithoutConsultations->id, $resultIds);
        $this->assertNotContains($activeWithConsultations->id, $resultIds);
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
