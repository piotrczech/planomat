<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\DeanOffice;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;

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
