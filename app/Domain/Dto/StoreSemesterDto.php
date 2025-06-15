<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use App\Domain\Enums\SemesterSeasonEnum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Illuminate\Validation\Rule;

// Potrzebne do budowania reguły unikalności

final class StoreSemesterDto extends Data
{
    public function __construct(
        #[Required]
        #[IntegerType]
        public readonly int $start_year,
        #[Required]
        public readonly string $season,
        #[Required]
        public readonly string $semester_start_date,
        #[Required]
        public readonly string $session_start_date,
        #[Required]
        public readonly string $end_date,
    ) {
    }

    public static function rules(array $context): array
    {
        return [
            'start_year' => [
                'required',
                'integer',
                'digits:4', // Rok powinien mieć 4 cyfry
                'between:1901,2155', // Dodana reguła zakresu dla typu YEAR
                Rule::unique('semesters')->where(function ($query) use ($context) {
                    return $query->where('season', $context['season'] ?? null);
                }),
            ],
            'season' => ['required', new \Illuminate\Validation\Rules\Enum(SemesterSeasonEnum::class)],
            'semester_start_date' => ['required', 'date_format:Y-m-d'],
            'session_start_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:semester_start_date'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:session_start_date'],
        ];
    }

    public static function messages(): array
    {
        return [
            'start_year.required' => __('admin_settings.semester_manager.validation.start_year_required'),
            'start_year.integer' => __('admin_settings.semester_manager.validation.start_year_integer'),
            'start_year.digits' => __('admin_settings.semester_manager.validation.start_year_digits'),
            'start_year.between' => __('admin_settings.semester_manager.validation.start_year_between'),
            'start_year.unique' => __('admin_settings.semester_manager.validation.year_season_unique'),
            'season.required' => __('admin_settings.semester_manager.validation.season_required'),
            'season.enum' => __('admin_settings.semester_manager.validation.season_enum'),
            'semester_start_date.required' => __('admin_settings.semester_manager.validation.semester_start_date_required'),
            'semester_start_date.date_format' => __('admin_settings.semester_manager.validation.semester_start_date_date_format'),
            'session_start_date.required' => __('admin_settings.semester_manager.validation.session_start_date_required'),
            'session_start_date.date_format' => __('admin_settings.semester_manager.validation.session_start_date_date_format'),
            'session_start_date.after_or_equal' => __('admin_settings.semester_manager.validation.session_start_date_after_or_equal_semester_start'),
            'end_date.required' => __('admin_settings.semester_manager.validation.end_date_required'),
            'end_date.date_format' => __('admin_settings.semester_manager.validation.end_date_date_format'),
            'end_date.after_or_equal' => __('admin_settings.semester_manager.validation.end_date_after_or_equal_session_start'),
        ];
    }
}
