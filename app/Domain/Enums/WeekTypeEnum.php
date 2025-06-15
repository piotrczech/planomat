<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum WeekTypeEnum: string
{
    case ALL = 'all';
    case ODD = 'odd';
    case EVEN = 'even';

    public function label(): string
    {
        return match ($this) {
            self::ALL => __('weektype.all'),
            self::ODD => __('weektype.odd'),
            self::EVEN => __('weektype.even'),
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::ALL => __('weektype.all_short'),
            self::ODD => __('weektype.odd_short'),
            self::EVEN => __('weektype.even_short'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
