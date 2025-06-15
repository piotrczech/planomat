<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum ActivityLogModuleEnum: string
{
    case CONSULTATION = 'consultation';
    case DESIDERATA = 'desiderata';
    case SEMESTER = 'semester';

    public function label(): string
    {
        return match ($this) {
            self::CONSULTATION => __('activity_log.module.consultation'),
            self::DESIDERATA => __('activity_log.module.desiderata'),
            self::SEMESTER => __('activity_log.module.semester'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
