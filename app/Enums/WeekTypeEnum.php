<?php

declare(strict_types=1);

namespace App\Enums;

enum WeekTypeEnum: string
{
    case ALL = 'all';
    case ODD = 'odd';
    case EVEN = 'even';

    public function label(): string
    {
        return match ($this) {
            self::ALL => __('week_type.all'),
            self::ODD => __('week_type.odd'),
            self::EVEN => __('week_type.even'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
