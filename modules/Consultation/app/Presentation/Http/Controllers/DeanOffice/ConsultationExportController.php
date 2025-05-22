<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Http\Controllers\DeanOffice;

use App\Http\Controllers\Controller; // Zakładam, że masz bazowy Controller
use Modules\Consultation\Application\UseCases\DeanOffice\ExportAllConsultationsToPdfUseCase;
use Symfony\Component\HttpFoundation\Response;

final class ConsultationExportController extends Controller
{
    public function downloadAllPdf(ExportAllConsultationsToPdfUseCase $exportAllConsultationsToPdfUseCase): Response
    {
        // Można tu dodać autoryzację, jeśli nie jest globalnie w middleware
        // nap. $this->authorize('exportAllConsultations', Consultation::class);

        return $exportAllConsultationsToPdfUseCase->execute();
    }
}
