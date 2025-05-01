<?php

declare(strict_types=1);

namespace App\Enums;

enum WeekdayEnum: string
{
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';

    public function label(): string
    {
        return match ($this) {
            self::MONDAY => __('weekday.monday'),
            self::TUESDAY => __('weekday.tuesday'),
            self::WEDNESDAY => __('weekday.wednesday'),
            self::THURSDAY => __('weekday.thursday'),
            self::FRIDAY => __('weekday.friday'),
            self::SATURDAY => __('weekday.saturday'),
            self::SUNDAY => __('weekday.sunday'),
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::MONDAY => __('weekday.monday_short'),
            self::TUESDAY => __('weekday.tuesday_short'),
            self::WEDNESDAY => __('weekday.wednesday_short'),
            self::THURSDAY => __('weekday.thursday_short'),
            self::FRIDAY => __('weekday.friday_short'),
            self::SATURDAY => __('weekday.saturday_short'),
            self::SUNDAY => __('weekday.sunday_short'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
