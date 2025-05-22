<?php

declare(strict_types=1);

namespace Modules\Consultation\Infrastructure\Services;

use Barryvdh\DomPDF\Facade\Pdf as DomPdfFacade;
use Modules\Consultation\Domain\Interfaces\Services\PdfGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

class DomPdfGenerator implements PdfGeneratorInterface
{
    public function generateFromView(
        string $view,
        array $data = [],
        string $filename = 'document.pdf',
        string $orientation = 'portrait',
        string $paperSize = 'a4',
    ): Response {
        $pdfLoaded = DomPdfFacade::loadView($view, $data);
        $pdfLoaded->setPaper($paperSize, $orientation);

        return $pdfLoaded->download($filename);
    }
}
