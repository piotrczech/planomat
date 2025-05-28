<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Illuminate\Validation\Rule as IlluminateRule;

 // Keep for constructing the unique rule string

final class UpdateCourseDto extends Data
{
    public function __construct(
        #[Required] // Assuming ID is always present for an update
        public readonly int $id,
        #[Required]
        #[StringType]
        #[Max(255)]
        // The unique rule needs to ignore the current course ID.
        // This will be constructed dynamically in the rules method.
        public readonly string $name,
    ) {
    }

    public static function rules(array $context): array
    {
        // $context will contain the input data, including 'id' if provided.
        // However, for an update DTO, id is part of constructor and should be available via $this if needed,
        // but static rules method doesn't have $this. We need to get id from $context if available.
        $courseId = $context['id'] ?? null; // Get ID from the payload being validated

        return [
            'id' => ['required', 'integer', 'exists:courses,id'], // Ensure the ID exists
            'name' => [
                'required',
                'string',
                'max:255',
                // If $courseId is available from context, use it. Otherwise, this DTO might be misused.
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

    // Optionally, you can include an authorization method if needed for this specific DTO
    // public static function authorize(): bool
    // {
    //     return true; // Or some specific authorization logic
    // }
}
