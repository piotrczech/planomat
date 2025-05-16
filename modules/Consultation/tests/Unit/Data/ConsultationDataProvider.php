<?php

declare(strict_types=1);

namespace Modules\Consultation\Tests\Unit\Data;

use App\Enums\WeekdayEnum;
use App\Enums\WeekTypeEnum;

class ConsultationDataProvider
{
    public static function weekdayDataProvider(): array
    {
        return [
            'monday' => [WeekdayEnum::MONDAY->value, WeekTypeEnum::ALL->value],
            'tuesday' => [WeekdayEnum::TUESDAY->value, WeekTypeEnum::ALL->value],
            'wednesday' => [WeekdayEnum::WEDNESDAY->value, WeekTypeEnum::ALL->value],
            'thursday' => [WeekdayEnum::THURSDAY->value, WeekTypeEnum::ALL->value],
            'friday' => [WeekdayEnum::FRIDAY->value, WeekTypeEnum::ALL->value],
        ];
    }

    public static function weekTypeDataProvider(): array
    {
        return [
            'all weeks' => [WeekTypeEnum::ALL->value],
            'even weeks' => [WeekTypeEnum::EVEN->value],
            'odd weeks' => [WeekTypeEnum::ODD->value],
        ];
    }

    public static function consultationDurationDataProvider(): array
    {
        return [
            'minimum time (60 min)' => ['09:00', '10:00', true],
            'maximum time (180 min)' => ['09:00', '12:00', true],
            'standard time (90 min)' => ['09:00', '10:30', true],
            'too short time (30 min)' => ['09:00', '09:30', false],
            'too long time (240 min)' => ['09:00', '13:00', false],
        ];
    }
}
