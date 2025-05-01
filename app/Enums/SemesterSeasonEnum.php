<?php

declare(strict_types=1);

namespace App\Enums;

enum SemesterSeasonEnum: int
{
    case SPRING = 1;
    case WINTER = 2;

    /**
     * Get human readable season name
     */
    public function label(): string
    {
        return match ($this) {
            self::SPRING => __('semester_season.spring'),
            self::WINTER => __('semester_season.winter'),
        };
    }

    /**
     * Get all available seasons
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
