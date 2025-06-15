<?php

declare(strict_types=1);

namespace Modules\Consultation\Domain\Dto;

use App\Infrastructure\Models\Semester;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\After;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class CreateNewSessionConsultationDto extends Data
{
    public function __construct(
        #[Required]
        #[StringType]
        #[Date]
        public string $consultationDate,
        #[Required]
        #[StringType]
        #[Regex('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/')]
        public string $consultationStartTime,
        #[Required]
        #[StringType]
        #[Regex('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/')]
        #[After('consultationStartTime')]
        public string $consultationEndTime,
        #[Required]
        #[StringType]
        #[Min(2)]
        public string $consultationLocation,
    ) {
    }

    public static function rules(): array
    {
        return [
            'consultationStartTime' => [
                'required',
                'string',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
                function ($attribute, $value, $fail): void {
                    $startTime = Carbon::createFromFormat('H:i', $value);
                    $minTime = Carbon::createFromFormat('H:i', '07:30');
                    $maxTime = Carbon::createFromFormat('H:i', '19:30');

                    if ($startTime->lt($minTime) || $startTime->gt($maxTime)) {
                        $fail(__('consultation::consultation.The consultation start time must be between 7:30 and 19:30'));
                    }
                },
            ],
            'consultationEndTime' => [
                'required',
                'string',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
                'after:consultationStartTime',
                function ($attribute, $value, $fail): void {
                    $endTime = Carbon::createFromFormat('H:i', $value);
                    $minTime = Carbon::createFromFormat('H:i', '08:30');
                    $maxTime = Carbon::createFromFormat('H:i', '20:30');

                    if ($endTime->lt($minTime) || $endTime->gt($maxTime)) {
                        $fail(__('consultation::consultation.The consultation end time must be between 8:30 and 20:30'));
                    }
                },
                function ($attribute, $value, $fail): void {
                    $formData = request()->all();

                    if (isset($formData['consultationStartTime'])) {
                        $startTime = Carbon::createFromFormat('H:i', $formData['consultationStartTime']);
                        $endTime = Carbon::createFromFormat('H:i', $value);
                        $durationInMinutes = $endTime->diffInMinutes($startTime);

                        if ($durationInMinutes < 60) {
                            $fail(__('consultation::consultation.The consultation must be at least 60 minutes long'));
                        }

                        if ($durationInMinutes > 180) {
                            $fail(__('consultation::consultation.The consultation cannot be longer than 180 minutes'));
                        }
                    }
                },
            ],
            'consultationLocation' => [
                'required',
                'string',
                'min:2',
            ],
            'consultationDate' => [
                'required',
                'date',
                function ($attribute, $value, $fail): void {
                    $consultationDate = Carbon::parse($value);

                    $semester = Semester::getCurrentSemester();
                    $sessionStart = Carbon::parse($semester->session_start_date);
                    $sessionEnd = Carbon::parse($semester->end_date);

                    if ($consultationDate->lt($sessionStart) || $consultationDate->gt($sessionEnd)) {
                        $fail(__('consultation::consultation.Date must be between session dates'));
                    }
                },
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'consultationDate.required' => __('consultation::consultation.Consultation date is required'),
            'consultationDate.date' => __('consultation::consultation.Invalid date format'),
            'consultationStartTime.required' => __('consultation::consultation.Start time is required'),
            'consultationStartTime.regex' => __('consultation::consultation.Invalid time format. Use format: HH:MM'),
            'consultationEndTime.required' => __('consultation::consultation.End time is required'),
            'consultationEndTime.regex' => __('consultation::consultation.Invalid time format. Use format: HH:MM'),
            'consultationEndTime.after' => __('consultation::consultation.End time must be after start time'),
            'consultationLocation.required' => __('consultation::consultation.Location is required'),
            'consultationLocation.min' => __('consultation::consultation.Location must be at least 2 characters long'),
        ];
    }
}
