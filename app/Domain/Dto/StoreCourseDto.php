<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Rule;

final class StoreCourseDto extends Data
{
    public function __construct(
        #[Required]
        #[StringType]
        #[Max(255)]
        #[Rule('unique:courses,name')]
        public readonly string $name,
    ) {
    }

    public static function messages(): array
    {
        return [
            'name.required' => __('admin_settings.validation_name_required'),
            'name.string' => __('admin_settings.validation_name_string'),
            'name.max' => __('admin_settings.validation_name_max'),
            'name.unique' => __('admin_settings.validation_name_unique'),
        ];
    }
}
