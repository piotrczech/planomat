<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Http\Controllers\DeanOffice;

use App\Http\Controllers\Controller; // Zakładam, że masz bazowy Controller
use Modules\Consultation\Application\UseCases\DeanOffice\ExportAllConsultationsToPdfUseCase;
use Modules\Consultation\Application\UseCases\DeanOffice\ExportUnfilledConsultationsToPdfUseCase;
use Symfony\Component\HttpFoundation\Response;

final class ConsultationExportController extends Controller
{
    public function __construct(
        private readonly ExportAllConsultationsToPdfUseCase $exportAllConsultationsToPdfUseCase,
        private readonly ExportUnfilledConsultationsToPdfUseCase $exportUnfilledConsultationsToPdfUseCase,
    ) {
    }

    public function downloadAllPdf(int $semester, string $type): Response
    {
        // Można tu dodać autoryzację, jeśli nie jest globalnie w middleware
        // nap. $this->authorize('exportAllConsultations', Consultation::class);

        return $this->exportAllConsultationsToPdfUseCase->execute($semester, $type);
    }

    public function downloadUnfilledPdf(int $semester, string $type): Response
    {
        return $this->exportUnfilledConsultationsToPdfUseCase->execute($semester, $type);
    }
}
