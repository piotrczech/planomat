<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\DeanOffice;

use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Domain\Interfaces\Services\PdfGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

final class ExportAllConsultationsToPdfUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly PdfGeneratorInterface $pdfGenerator,
    ) {
    }

    public function execute(): Response
    {
        $groupedConsultations = $this->consultationRepository->fetchAllForPdfExport();

        $dataForPdf = [
            'groupedConsultations' => $groupedConsultations,
            'reportDate' => Carbon::now()->translatedFormat('d F Y H:i'),
        ];

        $filename = 'raport_konsultacji_-' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';

        return $this->pdfGenerator->generateFromView(
            view: 'consultation::pdf.all_consultations',
            data: $dataForPdf,
            filename: $filename,
            orientation: 'portrait',
            paperSize: 'a4',
        );
    }
}
