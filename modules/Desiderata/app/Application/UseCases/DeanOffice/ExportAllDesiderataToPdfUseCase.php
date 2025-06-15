<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\DeanOffice;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Interfaces\SemesterRepositoryInterface;

final class ExportAllDesiderataToPdfUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
        private readonly SemesterRepositoryInterface $semesterRepository,
    ) {
    }

    public function execute(int $semesterId): Response
    {
        $scientificWorkers = $this->desideratumRepository->getAllDesiderataForPdfExport($semesterId);
        $semester = $this->semesterRepository->findOrFail($semesterId);
        $reportDate = Carbon::now()->translatedFormat('d F Y H:i');

        $pdf = Pdf::loadView('desiderata::pdf.all_desiderata_export', [
            'scientificWorkers' => $scientificWorkers,
            'reportDate' => $reportDate,
            'semester' => $semester,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('planomat_dezyderaty_semestr_' . $semester->name . '_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
}
