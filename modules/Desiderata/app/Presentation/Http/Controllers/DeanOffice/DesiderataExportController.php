<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Http\Controllers\DeanOffice;

use App\Http\Controllers\Controller;
use Modules\Desiderata\Application\UseCases\DeanOffice\ExportAllDesiderataToPdfUseCase;
use Symfony\Component\HttpFoundation\Response;

final class DesiderataExportController extends Controller
{
    public function __construct(
        private readonly ExportAllDesiderataToPdfUseCase $exportAllDesiderataToPdfUseCase,
    ) {
    }

    public function downloadAllPdf(): Response
    {
        return $this->exportAllDesiderataToPdfUseCase->execute();
    }
}
