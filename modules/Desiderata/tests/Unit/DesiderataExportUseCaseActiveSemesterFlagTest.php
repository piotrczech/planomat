<?php

declare(strict_types=1);

namespace Modules\Desiderata\Tests\Unit;

use App\Application\UseCases\Course\GetAllCoursesUseCase;
use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use App\Domain\Enums\SemesterSeasonEnum;
use App\Domain\Interfaces\CourseRepositoryInterface;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Domain\Interfaces\SettingRepositoryInterface;
use App\Domain\Interfaces\Services\PdfGeneratorInterface;
use App\Infrastructure\Models\Semester;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Modules\Desiderata\Application\UseCases\DeanOffice\ExportAllDesiderataToPdfUseCase;
use Modules\Desiderata\Application\UseCases\DeanOffice\ExportUnfilledDesiderataToPdfUseCase;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DesiderataExportUseCaseActiveSemesterFlagTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_all_desiderata_pdf_passes_true_for_active_semester(): void
    {
        $semesterId = 5;

        $desideratumRepository = Mockery::mock(DesideratumRepositoryInterface::class);
        $desideratumRepository
            ->shouldReceive('countScientificWorkersForPdfExport')
            ->once()
            ->with($semesterId, true)
            ->andReturn(1);
        $desideratumRepository
            ->shouldReceive('getAllDesiderataForPdfExport')
            ->once()
            ->with($semesterId, true)
            ->andReturn(collect());

        $semesterRepository = Mockery::mock(SemesterRepositoryInterface::class);
        $semesterRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($semesterId)
            ->andReturn($this->makeSemester($semesterId));

        $courseRepository = Mockery::mock(CourseRepositoryInterface::class);
        $courseRepository
            ->shouldReceive('getAllCourses')
            ->once()
            ->andReturn(new EloquentCollection);

        $pdfGenerator = Mockery::mock(PdfGeneratorInterface::class);
        $pdfGenerator
            ->shouldReceive('generateFromView')
            ->once()
            ->andReturn(new Response('', 200));

        $useCase = new ExportAllDesiderataToPdfUseCase(
            desideratumRepository: $desideratumRepository,
            semesterRepository: $semesterRepository,
            getAllCoursesUseCase: new GetAllCoursesUseCase($courseRepository),
            pdfGenerator: $pdfGenerator,
            getActiveDesiderataSemesterUseCase: $this->createActiveDesiderataSemesterUseCase($semesterId),
        );

        $response = $useCase->execute($semesterId);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_unfilled_desiderata_pdf_passes_false_for_historical_semester(): void
    {
        $semesterId = 5;

        $desideratumRepository = Mockery::mock(DesideratumRepositoryInterface::class);
        $desideratumRepository
            ->shouldReceive('getScientificWorkersWithoutDesiderata')
            ->once()
            ->with($semesterId, false)
            ->andReturn(collect());

        $pdfGenerator = Mockery::mock(PdfGeneratorInterface::class);
        $pdfGenerator
            ->shouldReceive('generateFromView')
            ->once()
            ->andReturn(new Response('', 200));

        $useCase = new ExportUnfilledDesiderataToPdfUseCase(
            desideratumRepository: $desideratumRepository,
            pdfGenerator: $pdfGenerator,
            getActiveDesiderataSemesterUseCase: $this->createActiveDesiderataSemesterUseCase($semesterId + 1),
        );

        $response = $useCase->execute($semesterId);

        $this->assertSame(200, $response->getStatusCode());
    }

    private function createActiveDesiderataSemesterUseCase(?int $activeSemesterId): GetActiveDesiderataSemesterUseCase
    {
        $settingRepository = Mockery::mock(SettingRepositoryInterface::class);
        $settingRepository
            ->shouldReceive('getSettings')
            ->once()
            ->with(['active_semester_for_desiderata_id'])
            ->andReturn(collect([
                'active_semester_for_desiderata_id' => $activeSemesterId === null ? null : (string) $activeSemesterId,
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

        return new GetActiveDesiderataSemesterUseCase(
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
