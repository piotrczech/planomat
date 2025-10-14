<?php

declare(strict_types=1);

namespace Modules\Consultation\Domain\Dto;

use App\Domain\Enums\WeekTypeEnum;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\After;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Auth;
use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use Modules\Consultation\Infrastructure\Models\SemesterConsultation;

final class CreateNewSemesterConsultationDto extends Data
{
    public function __construct(
        #[Required]
        #[StringType]
        public string $consultationWeekday,
        #[Required]
        #[StringType]
        public string $dailyConsultationWeekType,
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
        public string $consultationLocationBuilding,
        #[Required]
        #[StringType]
        public ?string $consultationLocationRoom,
    ) {
    }

    public static function rules($context): array
    {
        return [
            'consultationWeekday' => [
                function ($attribute, $value, $fail) use ($context): void {
                    $startTime = $context->fullPayload['consultationStartTime'];
                    $endTime = $context->fullPayload['consultationEndTime'];

                    if (!$startTime || !$endTime) {
                        return;
                    }

                    $userId = Auth::id();
                    /** @var \App\Infrastructure\Models\Semester|null $semester */
                    $semester = app(GetActiveConsultationSemesterUseCase::class)->execute();

                    if (!$semester) {
                        return;
                    }

                    $semesterId = $semester->id;

                    $query = SemesterConsultation::where('scientific_worker_id', $userId)
                        ->where('semester_id', $semesterId)
                        ->where(function ($q) use ($startTime, $endTime): void {
                            $q->where('start_time', '<', $endTime)
                                ->where('end_time', '>', $startTime);
                        });

                    $newWeekType = $context->fullPayload['dailyConsultationWeekType'];

                    $query->where('day', $value)
                        ->where(function ($q) use ($newWeekType): void {
                            // Check for collisions with consultations of type 'all' (always collide)
                            $q->where('week_type', WeekTypeEnum::ALL->value);

                            // Check for collisions with consultations of the same type of week
                            if ($newWeekType !== WeekTypeEnum::ALL->value) {
                                $q->orWhere('week_type', $newWeekType);
                            }

                            // If the new consultation has type 'all', it collides with all types
                            if ($newWeekType === WeekTypeEnum::ALL->value) {
                                $q->orWhere('week_type', WeekTypeEnum::ODD->value)
                                    ->orWhere('week_type', WeekTypeEnum::EVEN->value);
                            }
                        });

                    if ($query->exists()) {
                        $fail(__('consultation::consultation.A consultation with a conflicting time already exists.'));
                    }
                },
            ],
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
                function ($attribute, $value, $fail) use ($context): void {
                    $startTimeValue = $context->payload['consultationStartTime'] ?? null;

                    if ($startTimeValue) {
                        $startTime = Carbon::createFromFormat('H:i', $startTimeValue);
                        $endTime = Carbon::createFromFormat('H:i', $value);
                        $durationInMinutes = abs($endTime->diffInMinutes($startTime));

                        if ($durationInMinutes < 60) {
                            $fail(__('consultation::consultation.The consultation must be at least 60 minutes long'));
                        }

                        if ($durationInMinutes > 180) {
                            $fail(__('consultation::consultation.The consultation cannot be longer than 180 minutes'));
                        }
                    }
                },
            ],
            'consultationLocationRoom' => [
                'required',
                'string',
                'min:1',
                'max:100',
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'consultationWeekday.required' => __('consultation::consultation.Weekday is required'),
            'consultationWeekday.in' => __('consultation::consultation.Invalid weekday selected'),
            'dailyConsultationWeekType.required_if' => __('consultation::consultation.Week type is required for weekday consultations'),
            'dailyConsultationWeekType.in' => __('consultation::consultation.Invalid week type selected'),
            'consultationStartTime.required' => __('consultation::consultation.Start time is required'),
            'consultationStartTime.regex' => __('consultation::consultation.Invalid time format. Use format: HH:MM'),
            'consultationEndTime.required' => __('consultation::consultation.End time is required'),
            'consultationEndTime.regex' => __('consultation::consultation.Invalid time format. Use format: HH:MM'),
            'consultationEndTime.after' => __('consultation::consultation.End time must be after start time'),
            'consultationLocationBuilding.required' => __('consultation::consultation.Location is required'),
            'consultationLocationBuilding.min' => __('consultation::consultation.Location must be at least 2 characters long'),
            'consultationLocationRoom.required' => __('consultation::consultation.Consultation location room is required'),
            'consultationLocationRoom.min' => __('consultation::consultation.Room must be at least 1 character long'),
            'consultationLocationRoom.max' => __('consultation::consultation.Room cannot be longer than 100 characters'),
        ];
    }
}
