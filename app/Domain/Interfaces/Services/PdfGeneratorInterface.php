<?php

declare(strict_types=1);

namespace App\Domain\Interfaces\Services;

use Symfony\Component\HttpFoundation\Response;

interface PdfGeneratorInterface
{
    public function generateFromView(
        string $view,
        array $data = [],
        string $filename = 'document.pdf',
        string $orientation = 'portrait',
        string $paperSize = 'a4',
    ): Response;
}
