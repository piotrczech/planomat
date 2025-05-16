<?php

declare(strict_types=1);

namespace Modules\Desiderata\Tests\Unit\Data;

class DesideratumDataProvider
{
    public static function desideratumConfigurationsProvider(): array
    {
        return [
            'minimum data' => [
                [
                    'wantStationary' => true,
                    'wantNonStationary' => false,
                    'agreeToOvertime' => false,
                ],
            ],
            'full data' => [
                [
                    'wantStationary' => true,
                    'wantNonStationary' => true,
                    'agreeToOvertime' => true,
                    'unwantedCourseIds' => [1, 2],
                    'wantedCourseIds' => [3, 4, 5],
                    'proficientCourseIds' => [1, 2, 3, 4, 5, 6],
                    'masterThesesCount' => 2,
                    'bachelorThesesCount' => 3,
                    'maxHoursPerDay' => 8,
                    'maxConsecutiveHours' => 4,
                    'unavailableTimeSlots' => ['Monday_8:00', 'Tuesday_9:00'],
                    'additionalNotes' => 'Additional notes',
                ],
            ],
            'only stationary teaching' => [
                [
                    'wantStationary' => true,
                    'wantNonStationary' => false,
                    'agreeToOvertime' => true,
                    'unwantedCourseIds' => [2, 3],
                    'wantedCourseIds' => [1, 4],
                    'proficientCourseIds' => [1, 2, 3, 4],
                    'masterThesesCount' => 1,
                    'bachelorThesesCount' => 2,
                    'maxHoursPerDay' => 6,
                    'maxConsecutiveHours' => 3,
                    'unavailableTimeSlots' => ['Friday_15:00', 'Friday_16:00'],
                    'additionalNotes' => null,
                ],
            ],
            'only non-stationary teaching' => [
                [
                    'wantStationary' => false,
                    'wantNonStationary' => true,
                    'agreeToOvertime' => false,
                    'unwantedCourseIds' => [],
                    'wantedCourseIds' => [5, 6],
                    'proficientCourseIds' => [5, 6, 7],
                    'masterThesesCount' => 0,
                    'bachelorThesesCount' => 0,
                    'maxHoursPerDay' => 4,
                    'maxConsecutiveHours' => 2,
                    'unavailableTimeSlots' => [],
                    'additionalNotes' => 'I prefer weekend classes',
                ],
            ],
        ];
    }

    public static function maximumUnavailableSlotsProvider(): array
    {
        return [
            'maximum slots' => [
                [
                    'wantStationary' => true,
                    'wantNonStationary' => false,
                    'agreeToOvertime' => false,
                    'unwantedCourseIds' => [1],
                    'wantedCourseIds' => [2],
                    'proficientCourseIds' => [1, 2, 3],
                    'masterThesesCount' => 1,
                    'bachelorThesesCount' => 1,
                    'maxHoursPerDay' => 6,
                    'maxConsecutiveHours' => 3,
                    'unavailableTimeSlots' => [
                        'Monday_8:00',
                        'Tuesday_10:00',
                        'Wednesday_12:00',
                        'Thursday_14:00',
                        'Friday_16:00',
                    ],
                    'additionalNotes' => 'Maximum number of unavailable slots',
                ],
            ],
        ];
    }
}
