<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use Illuminate\Support\Collection;

final readonly class EmailNotificationDto
{
    public function __construct(
        public Collection $recipients,
        public string $subject,
        public string $template,
        public array $data = [],
    ) {
    }
}
