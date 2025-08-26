<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\DeanOffice;

use Modules\Consultation\Application\Services\ConsultationReportService;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use App\Domain\Interfaces\Services\PdfGeneratorInterface;
use Modules\Consultation\Domain\Enums\ConsultationType;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Domain\Interfaces\SemesterRepositoryInterface;

final class ExportAllConsultationsToPdfUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly PdfGeneratorInterface $pdfGenerator,
        private readonly ConsultationReportService $reportService,
        private readonly SemesterRepositoryInterface $semesterRepository,
    ) {
    }

    public function execute(int $semesterId, string $type): Response
    {
        $consultationType = ConsultationType::tryFrom($type);

        if (!$consultationType) {
            throw ValidationException::withMessages(['type' => 'Invalid consultation type provided.']);
        }

        $scientificWorkers = $this->consultationRepository->getAllScientificWorkersWithConsultations($semesterId, $consultationType);

        $processedData = $this->reportService->prepareAllConsultationsReportData($scientificWorkers, $consultationType);

        $semester = $this->semesterRepository->findOrFail($semesterId);

        $dataForPdf = [
            'processedWorkers' => $processedData,
            'reportDate' => Carbon::now()->translatedFormat('d F Y H:i'),
            'consultationType' => $consultationType,
            'semester' => $semester,
        ];

        $filename = 'raport_konsultacji_' . mb_strtolower($semester->season->label()) . '_' . str_replace('/', '_', $semester->academic_year) . '__' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $this->pdfGenerator->generateFromView(
            view: 'consultation::pdf.all_consultations',
            data: $dataForPdf,
            filename: $filename,
            orientation: 'portrait',
            paperSize: 'a4',
        );
    }
}
