<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Illuminate\Validation\Rule as IlluminateRule;

final class UpdateCourseDto extends Data
{
    public function __construct(
        #[Required]
        public readonly int $id,
        #[Required]
        #[StringType]
        #[Max(255)]
        public readonly string $name,
    ) {
    }

    public static function rules(array $context): array
    {
        $courseId = $context['id'] ?? null;

        return [
            'id' => ['required', 'integer', 'exists:courses,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                $courseId ? IlluminateRule::unique('courses', 'name')->ignore($courseId) : 'unique:courses,name',
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'id.required' => __('admin_settings.validation_id_required'),
            'id.integer' => __('admin_settings.validation_id_integer'),
            'id.exists' => __('admin_settings.validation_id_exists'),
            'name.required' => __('admin_settings.validation_name_required'),
            'name.string' => __('admin_settings.validation_name_string'),
            'name.max' => __('admin_settings.validation_name_max'),
            'name.unique' => __('admin_settings.validation_name_unique'),
        ];
    }
}
