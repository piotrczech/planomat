<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use App\Infrastructure\Models\User;
use App\Infrastructure\Models\Semester;

final readonly class MissingDataNotificationDto
{
    public function __construct(
        public User $user,
        public Semester $semester,
        public string $type, // 'semester_consultations', 'session_consultations', 'desiderata'
        public int $daysOverdue,
    ) {
    }
}
