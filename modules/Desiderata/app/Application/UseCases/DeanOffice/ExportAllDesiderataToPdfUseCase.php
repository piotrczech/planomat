<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\DeanOffice;

use App\Application\UseCases\Course\GetAllCoursesUseCase;
use Carbon\Carbon;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use Modules\Consultation\Domain\Interfaces\Services\PdfGeneratorInterface;

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
        $scientificWorkers = $this->desideratumRepository->getAllDesiderataForPdfExport($semesterId);
        $semester = $this->semesterRepository->findOrFail($semesterId);
        $reportDate = Carbon::now()->translatedFormat('d F Y H:i');
        $allCourses = $this->getAllCoursesUseCase->execute();

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
}
