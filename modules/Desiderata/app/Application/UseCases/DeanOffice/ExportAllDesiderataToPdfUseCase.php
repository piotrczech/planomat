<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\DeanOffice;

use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Desiderata\Domain\Repository\DesideratumRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

final class ExportAllDesiderataToPdfUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
    ) {
    }

    public function execute(): Response
    {
        $allDesiderata = $this->desideratumRepository->getAllDesiderataForPdfExport();
        $reportDate = Carbon::now()->translatedFormat('d F Y H:i');

        $pdf = Pdf::loadView('desiderata::pdf.all_desiderata_export', [
            'allDesiderata' => $allDesiderata,
            'reportDate' => $reportDate,
        ])->setPaper('a4');

        return $pdf->download('planomat_dezyderaty_-' . Carbon::now()->format('Y-m-d_H-i') . '.pdf');
    }
}
