<?php

declare(strict_types=1);

namespace Modules\Desiderata\Domain\Dto;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\ArrayType;

class UpdateOrCreateDesideratumDto extends Data
{
    public function __construct(
        #[Required, BooleanType]
        public readonly bool|null|Optional $wantStationary,
        #[Required, BooleanType]
        public readonly bool|null|Optional $wantNonStationary,
        #[Required, BooleanType]
        public readonly bool|null|Optional $agreeToOvertime,
        #[ArrayType]
        public readonly array|null|Optional $unwantedCourseIds,
        #[ArrayType]
        public readonly array|null|Optional $wantedCourseIds,
        #[ArrayType]
        public readonly array|null|Optional $proficientCourseIds,
        #[Required, Min(0), Max(20)]
        public readonly int|null|Optional $masterThesesCount,
        #[Required, Min(0), Max(20)]
        public readonly int|null|Optional $bachelorThesesCount,
        #[Required, Min(1), Max(12)]
        public readonly int|null|Optional $maxHoursPerDay,
        #[Required, Min(1), Max(8)]
        public readonly int|null|Optional $maxConsecutiveHours,
        #[ArrayType]
        public readonly array|null|Optional $unavailableTimeSlots,
        public readonly string|null|Optional $additionalNotes,
    ) {
    }

    public static function rules(): array
    {
        return [
            'wantStationary' => ['required', 'boolean'],
            'wantNonStationary' => ['required', 'boolean'],
            'agreeToOvertime' => ['required', 'boolean'],
            'unwantedCourseIds' => ['nullable', 'array', 'max:2'],
            'wantedCourseIds' => ['nullable', 'array'],
            'proficientCourseIds' => ['nullable', 'array'],
            'masterThesesCount' => ['required', 'integer', 'min:0', 'max:20'],
            'bachelorThesesCount' => ['required', 'integer', 'min:0', 'max:20'],
            'maxHoursPerDay' => ['required', 'integer', 'min:1', 'max:12'],
            'maxConsecutiveHours' => ['required', 'integer', 'min:1', 'max:8'],
            'unavailableTimeSlots' => ['nullable', 'array'],
            'additionalNotes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public static function messages(): array
    {
        return [
            'wantStationary.required' => __('desiderata::desiderata.The teaching mode is required'),
            'wantStationary.boolean' => __('desiderata::desiderata.The teaching mode must be a boolean value'),
            'wantNonStationary.required' => __('desiderata::desiderata.The teaching mode is required'),
            'wantNonStationary.boolean' => __('desiderata::desiderata.The teaching mode must be a boolean value'),
            'agreeToOvertime.required' => __('desiderata::desiderata.The overtime preference is required'),
            'agreeToOvertime.boolean' => __('desiderata::desiderata.The overtime preference must be a boolean value'),
            'unwantedCourseIds.max' => __('desiderata::desiderata.You can select at most 2 unwanted courses'),
            'masterThesesCount.required' => __('desiderata::desiderata.The number of master theses is required'),
            'masterThesesCount.integer' => __('desiderata::desiderata.The number of master theses must be a number'),
            'masterThesesCount.min' => __('desiderata::desiderata.The number of master theses must be at least 0'),
            'masterThesesCount.max' => __('desiderata::desiderata.The number of master theses cannot exceed 20'),
            'bachelorThesesCount.required' => __('desiderata::desiderata.The number of bachelor theses is required'),
            'bachelorThesesCount.integer' => __('desiderata::desiderata.The number of bachelor theses must be a number'),
            'bachelorThesesCount.min' => __('desiderata::desiderata.The number of bachelor theses must be at least 0'),
            'bachelorThesesCount.max' => __('desiderata::desiderata.The number of bachelor theses cannot exceed 20'),
            'maxHoursPerDay.required' => __('desiderata::desiderata.The maximum hours per day is required'),
            'maxHoursPerDay.integer' => __('desiderata::desiderata.The maximum hours per day must be a number'),
            'maxHoursPerDay.min' => __('desiderata::desiderata.The maximum hours per day must be at least 1'),
            'maxHoursPerDay.max' => __('desiderata::desiderata.The maximum hours per day cannot exceed 12'),
            'maxConsecutiveHours.required' => __('desiderata::desiderata.The maximum consecutive hours is required'),
            'maxConsecutiveHours.integer' => __('desiderata::desiderata.The maximum consecutive hours must be a number'),
            'maxConsecutiveHours.min' => __('desiderata::desiderata.The maximum consecutive hours must be at least 1'),
            'maxConsecutiveHours.max' => __('desiderata::desiderata.The maximum consecutive hours cannot exceed 8'),
            'additionalNotes.max' => __('desiderata::desiderata.Additional notes cannot exceed 1000 characters'),
        ];
    }
}
