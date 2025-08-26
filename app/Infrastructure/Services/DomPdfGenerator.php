<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use Barryvdh\DomPDF\Facade\Pdf as DomPdfFacade;
use App\Domain\Interfaces\Services\PdfGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

final class DomPdfGenerator implements PdfGeneratorInterface
{
    public function generateFromView(
        string $view,
        array $data = [],
        string $filename = 'document.pdf',
        string $orientation = 'portrait',
        string $paperSize = 'a4',
    ): Response {
        $options = [
            'isPhpEnabled' => false,
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
        ];

        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        $pdf = DomPdfFacade::setOptions($options)->loadView($view, $data);
        $pdf->setPaper($paperSize, $orientation);

        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        return $pdf->download($filename);
    }
}
