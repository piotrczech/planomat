<?php

declare(strict_types=1);

namespace Modules\Desiderata\Domain\Dto;

use Spatie\LaravelData\Data;

final class DesiderataFormAvailabilityDto extends Data
{
    public function __construct(
        public array $unavailableTimeSlots,
        public ?string $additionalNotes,
    ) {
    }

    public static function rules(): array
    {
        return [
            'unavailableTimeSlots' => ['nullable', 'array'],
            'unavailableTimeSlots.*' => ['array'],
            'additionalNotes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public static function messages(): array
    {
        return [
            'unavailableTimeSlots.array' => __('desiderata::desiderata.Unavailable time slots must be an array'),
            'additionalNotes.max' => __('desiderata::desiderata.Additional notes cannot exceed 1000 characters'),
        ];
    }
}
