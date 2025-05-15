<?php

declare(strict_types=1);

namespace App\Enums;

enum CoursePreferenceTypeEnum: int
{
    case WANTED = 1;
    case COULD = 2;
    case UNWANTED = 3;

    public function label(): string
    {
        return match ($this) {
            self::WANTED => __('course_preference_type.wanted'),
            self::COULD => __('course_preference_type.could'),
            self::UNWANTED => __('course_preference_type.not_want'),
        };
    }

    /**
     * Zwraca wszystkie wartości enum jako tablicę
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
