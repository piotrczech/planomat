<?php

declare(strict_types=1);

namespace Modules\Consultation\Presentation\Http\Controllers\DeanOffice;

use App\Presentation\Http\Controllers\Controller;
use Modules\Consultation\Application\UseCases\DeanOffice\ExportAllConsultationsToExcelUseCase;
use Modules\Consultation\Application\UseCases\DeanOffice\ExportAllConsultationsToPdfUseCase;
use Modules\Consultation\Application\UseCases\DeanOffice\ExportUnfilledConsultationsToPdfUseCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConsultationExportController extends Controller
{
    public function downloadAllPdf(Request $request, ExportAllConsultationsToPdfUseCase $useCase): Response
    {
        $semesterId = (int) $request->get('semester');
        $type = (string) $request->get('type');

        return $useCase->execute($semesterId, $type);
    }

    public function downloadAllExcel(Request $request, ExportAllConsultationsToExcelUseCase $useCase): Response
    {
        $semesterId = (int) $request->get('semester');
        $type = (string) $request->get('type');

        return $useCase->execute($semesterId, $type);
    }

    public function downloadUnfilledPdf(Request $request, ExportUnfilledConsultationsToPdfUseCase $useCase): Response
    {
        $semesterId = (int) $request->get('semester');
        $type = (string) $request->get('type');

        return $useCase->execute($semesterId, $type);
    }
}
