<?php

declare(strict_types=1);

namespace App\Enums;

enum CoursePreferenceTypeEnum: string
{
    case CAN = 'can';
    case WANT = 'want';
    case NOT_WANT = 'not_want';

    public function label(): string
    {
        return match ($this) {
            self::CAN => __('course_preference_type.can'),
            self::WANT => __('course_preference_type.want'),
            self::NOT_WANT => __('course_preference_type.not_want'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
