<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\DeanOffice;

use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Domain\Interfaces\Services\PdfGeneratorInterface;
use Modules\Consultation\Domain\Enums\ConsultationType;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

final class ExportUnfilledConsultationsToPdfUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly PdfGeneratorInterface $pdfGenerator,
    ) {
    }

    public function execute(int $semesterId, string $type): Response
    {
        $consultationType = ConsultationType::tryFrom($type);

        if (!$consultationType) {
            throw ValidationException::withMessages(['type' => 'Invalid consultation type provided.']);
        }

        $unfilledWorkers = $this->consultationRepository->getScientificWorkersWithoutConsultations($semesterId, $consultationType);

        $dataForPdf = [
            'unfilledWorkers' => $unfilledWorkers,
            'reportDate' => Carbon::now()->translatedFormat('d F Y H:i'),
            'consultationType' => $consultationType,
        ];

        $filename = 'raport_nieuzupelnione_' . $type . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';

        return $this->pdfGenerator->generateFromView(
            view: 'consultation::pdf.unfilled_consultations',
            data: $dataForPdf,
            filename: $filename,
            orientation: 'portrait',
            paperSize: 'a4',
        );
    }
}
