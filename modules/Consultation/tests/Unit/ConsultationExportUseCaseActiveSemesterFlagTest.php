<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Unit;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use App\Domain\Enums\SemesterSeasonEnum;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Domain\Interfaces\SettingRepositoryInterface;
use App\Domain\Interfaces\Services\PdfGeneratorInterface;
use App\Infrastructure\Models\Semester;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Maatwebsite\Excel\Facades\Excel;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Modules\Consultation\Application\Services\ConsultationReportService;
use Modules\Consultation\Application\UseCases\DeanOffice\ExportAllConsultationsToExcelUseCase;
use Modules\Consultation\Application\UseCases\DeanOffice\ExportAllConsultationsToPdfUseCase;
use Modules\Consultation\Application\UseCases\DeanOffice\ExportUnfilledConsultationsToPdfUseCase;
use Modules\Consultation\Domain\Enums\ConsultationType;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ConsultationExportUseCaseActiveSemesterFlagTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_all_consultations_pdf_passes_true_for_active_semester(): void
    {
        $semesterId = 5;

        $consultationRepository = Mockery::mock(ConsultationRepositoryInterface::class);
        $consultationRepository
            ->shouldReceive('getAllScientificWorkersWithConsultations')
            ->once()
            ->with($semesterId, ConsultationType::Session, true)
            ->andReturn(new EloquentCollection);

        $pdfGenerator = Mockery::mock(PdfGeneratorInterface::class);
        $pdfGenerator
            ->shouldReceive('generateFromView')
            ->once()
            ->andReturn(new Response('', 200));

        $semesterRepository = Mockery::mock(SemesterRepositoryInterface::class);
        $semesterRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($semesterId)
            ->andReturn($this->makeSemester($semesterId));

        $useCase = new ExportAllConsultationsToPdfUseCase(
            consultationRepository: $consultationRepository,
            pdfGenerator: $pdfGenerator,
            reportService: new ConsultationReportService,
            semesterRepository: $semesterRepository,
            getActiveConsultationSemesterUseCase: $this->createActiveConsultationSemesterUseCase($semesterId),
        );

        $response = $useCase->execute($semesterId, ConsultationType::Session->value);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_all_consultations_excel_passes_false_for_historical_semester(): void
    {
        $semesterId = 5;
        Carbon::setTestNow('2026-03-24 12:00:00');
        Excel::fake();

        $consultationRepository = Mockery::mock(ConsultationRepositoryInterface::class);
        $consultationRepository
            ->shouldReceive('getAllScientificWorkersWithConsultations')
            ->once()
            ->with($semesterId, ConsultationType::Session, false)
            ->andReturn(new EloquentCollection);

        $useCase = new ExportAllConsultationsToExcelUseCase(
            consultationRepository: $consultationRepository,
            getActiveConsultationSemesterUseCase: $this->createActiveConsultationSemesterUseCase($semesterId + 1),
        );

        $useCase->execute($semesterId, ConsultationType::Session->value);

        Excel::assertDownloaded('raport_konsultacji_session_2026-03-24_12-00-00.xlsx');
        Carbon::setTestNow();
    }

    public function test_unfilled_consultations_pdf_passes_true_for_active_semester(): void
    {
        $semesterId = 5;

        $consultationRepository = Mockery::mock(ConsultationRepositoryInterface::class);
        $consultationRepository
            ->shouldReceive('getScientificWorkersWithoutConsultations')
            ->once()
            ->with($semesterId, ConsultationType::Session, true)
            ->andReturn(new EloquentCollection);

        $pdfGenerator = Mockery::mock(PdfGeneratorInterface::class);
        $pdfGenerator
            ->shouldReceive('generateFromView')
            ->once()
            ->andReturn(new Response('', 200));

        $useCase = new ExportUnfilledConsultationsToPdfUseCase(
            consultationRepository: $consultationRepository,
            pdfGenerator: $pdfGenerator,
            getActiveConsultationSemesterUseCase: $this->createActiveConsultationSemesterUseCase($semesterId),
        );

        $response = $useCase->execute($semesterId, ConsultationType::Session->value);

        $this->assertSame(200, $response->getStatusCode());
    }

    private function createActiveConsultationSemesterUseCase(?int $activeSemesterId): GetActiveConsultationSemesterUseCase
    {
        $settingRepository = Mockery::mock(SettingRepositoryInterface::class);
        $settingRepository
            ->shouldReceive('getSettings')
            ->once()
            ->with(['active_semester_for_consultations_id'])
            ->andReturn(collect([
                'active_semester_for_consultations_id' => $activeSemesterId === null ? null : (string) $activeSemesterId,
            ]));

        $semesterRepository = Mockery::mock(SemesterRepositoryInterface::class);

        if ($activeSemesterId !== null) {
            $semesterRepository
                ->shouldReceive('findById')
                ->once()
                ->with($activeSemesterId)
                ->andReturn($this->makeSemester($activeSemesterId));
        } else {
            $semesterRepository->shouldReceive('findById')->never();
        }

        return new GetActiveConsultationSemesterUseCase(
            settingRepository: $settingRepository,
            semesterRepository: $semesterRepository,
        );
    }

    private function makeSemester(int $id): Semester
    {
        $semester = new Semester;
        $semester->id = $id;
        $semester->start_year = 2025;
        $semester->season = SemesterSeasonEnum::WINTER;

        return $semester;
    }
}
