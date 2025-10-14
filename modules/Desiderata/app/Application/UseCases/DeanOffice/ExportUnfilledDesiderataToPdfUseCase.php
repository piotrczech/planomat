<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\DeanOffice;

use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use App\Domain\Interfaces\Services\PdfGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

final class ExportUnfilledDesiderataToPdfUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
        private readonly PdfGeneratorInterface $pdfGenerator,
    ) {
    }

    public function execute(int $semesterId): Response
    {
        $unfilledWorkers = $this->desideratumRepository->getScientificWorkersWithoutDesiderata($semesterId);

        $dataForPdf = [
            'unfilledWorkers' => $unfilledWorkers,
            'reportDate' => Carbon::now()->translatedFormat('d F Y H:i'),
        ];

        $filename = 'raport_dezyderatów_nieuzupelnione_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';

        return $this->pdfGenerator->generateFromView(
            view: 'desiderata::pdf.unfilled_desiderata',
            data: $dataForPdf,
            filename: $filename,
            orientation: 'portrait',
            paperSize: 'a4',
        );
    }
}
