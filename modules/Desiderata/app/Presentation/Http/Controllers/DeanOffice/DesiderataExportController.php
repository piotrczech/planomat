<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Http\Controllers\DeanOffice;

use App\Presentation\Http\Controllers\Controller;
use Modules\Desiderata\Application\UseCases\DeanOffice\ExportAllDesiderataToPdfUseCase;
use Modules\Desiderata\Application\UseCases\DeanOffice\ExportUnfilledDesiderataToPdfUseCase;
use Symfony\Component\HttpFoundation\Response;

final class DesiderataExportController extends Controller
{
    public function __construct(
        private readonly ExportAllDesiderataToPdfUseCase $exportAllDesiderataToPdfUseCase,
        private readonly ExportUnfilledDesiderataToPdfUseCase $exportUnfilledDesiderataToPdfUseCase,
    ) {
    }

    public function downloadAllPdf(int $semester): Response
    {
        return $this->exportAllDesiderataToPdfUseCase->execute($semester);
    }

    public function downloadUnfilledPdf(int $semester): Response
    {
        return $this->exportUnfilledDesiderataToPdfUseCase->execute($semester);
    }
}
