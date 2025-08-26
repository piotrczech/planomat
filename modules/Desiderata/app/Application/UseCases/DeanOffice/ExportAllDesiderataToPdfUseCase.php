<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\DeanOffice;

use App\Application\UseCases\Course\GetAllCoursesUseCase;
use App\Domain\Enums\RoleEnum;
use Carbon\Carbon;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Domain\Interfaces\Services\PdfGeneratorInterface;

final class ExportAllDesiderataToPdfUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
        private readonly SemesterRepositoryInterface $semesterRepository,
        private readonly GetAllCoursesUseCase $getAllCoursesUseCase,
        private readonly PdfGeneratorInterface $pdfGenerator,
    ) {
    }

    public function execute(int $semesterId): Response
    {
        $previousMemoryLimit = ini_get('memory_limit');
        $previousMaxExecutionTime = ini_get('max_execution_time');

        @ini_set('memory_limit', '1024M');
        @ini_set('max_execution_time', '-1');

        try {
            $semester = $this->semesterRepository->findOrFail($semesterId);
            $reportDate = Carbon::now()->translatedFormat('d F Y H:i');
            $allCourses = $this->getAllCoursesUseCase->execute();

            $totalWorkers = User::where('role', RoleEnum::SCIENTIFIC_WORKER)->count();

            if ($totalWorkers <= 20) {
                return $this->generateStandardPdf($semesterId, $semester, $reportDate, $allCourses);
            }

            return $this->generateChunkedPdf($semesterId, $semester, $reportDate, $allCourses);
        } finally {
            @ini_set('memory_limit', (string) $previousMemoryLimit);
            @ini_set('max_execution_time', $previousMaxExecutionTime);
        }
    }

    private function generateStandardPdf(int $semesterId, object $semester, string $reportDate, \Illuminate\Support\Collection $allCourses): Response
    {
        $scientificWorkers = $this->desideratumRepository->getAllDesiderataForPdfExport($semesterId);

        return $this->pdfGenerator->generateFromView(
            view: 'desiderata::pdf.all_desiderata_export',
            data: [
                'scientificWorkers' => $scientificWorkers,
                'reportDate' => $reportDate,
                'semester' => $semester,
                'allCourses' => $allCourses,
            ],
            filename: 'raport_dezyderat_' . mb_strtolower($semester->season->label()) . '_' . str_replace('/', '_', $semester->academic_year) . '__' . Carbon::now()->format('Y-m-d') . '.pdf',
            orientation: 'landscape',
            paperSize: 'a4',
        );
    }

    private function generateChunkedPdf(int $semesterId, object $semester, string $reportDate, \Illuminate\Support\Collection $allCourses): Response
    {
        $allWorkers = collect();
        $chunkSize = 15;

        $this->desideratumRepository->getDesiderataForPdfExportChunked(
            $semesterId,
            $chunkSize,
            function ($workers) use ($allWorkers): void {
                $allWorkers->push(...$workers);

                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            },
        );

        return $this->pdfGenerator->generateFromView(
            view: 'desiderata::pdf.all_desiderata_export',
            data: [
                'scientificWorkers' => $allWorkers,
                'reportDate' => $reportDate,
                'semester' => $semester,
                'allCourses' => $allCourses,
            ],
            filename: 'raport_dezyderat_' . mb_strtolower($semester->season->label()) . '_' . str_replace('/', '_', $semester->academic_year) . '__' . Carbon::now()->format('Y-m-d') . '.pdf',
            orientation: 'landscape',
            paperSize: 'a4',
        );
    }
}
