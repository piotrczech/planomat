<?php

declare(strict_types=1);

namespace Modules\Consultation\Domain\Interfaces\Services;

use Symfony\Component\HttpFoundation\Response;

interface PdfGeneratorInterface
{
    /**
     * Generate a PDF document from a Blade view.
     *
     * @param  string  $view  The path to the Blade view.
     * @param  array  $data  The data to pass to the view.
     * @param  string  $filename  The desired filename for the downloaded PDF.
     * @param  string  $orientation  Page orientation (e.g., 'portrait', 'landscape').
     * @param  string  $paperSize  Paper size (e.g., 'a4', 'letter').
     */
    public function generateFromView(
        string $view,
        array $data = [],
        string $filename = 'document.pdf',
        string $orientation = 'portrait',
        string $paperSize = 'a4',
    ): Response;
}
