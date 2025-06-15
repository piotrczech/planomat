<?php

declare(strict_types=1);

namespace Modules\Consultation\Domain\Dto;

use App\Domain\Enums\WeekdayEnum;
use App\Domain\Enums\WeekTypeEnum;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\After;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Infrastructure\Models\ConsultationSemester;
use App\Infrastructure\Models\Semester;

final class CreateNewSemesterConsultationDto extends Data
{
    public function __construct(
        #[Required]
        #[StringType]
        #[In(WeekdayEnum::MONDAY->value, WeekdayEnum::TUESDAY->value, WeekdayEnum::WEDNESDAY->value, WeekdayEnum::THURSDAY->value, WeekdayEnum::FRIDAY->value, WeekdayEnum::SATURDAY->value, WeekdayEnum::SUNDAY->value)]
        public string $consultationWeekday,
        #[RequiredIf('consultationWeekday', 'in', [
            WeekdayEnum::MONDAY->value,
            WeekdayEnum::TUESDAY->value,
            WeekdayEnum::WEDNESDAY->value,
            WeekdayEnum::THURSDAY->value,
            WeekdayEnum::FRIDAY->value,
        ])]
        #[StringType]
        #[In(WeekTypeEnum::ALL->value, WeekTypeEnum::EVEN->value, WeekTypeEnum::ODD->value)]
        public string $dailyConsultationWeekType,
        #[RequiredIf('consultationWeekday', 'in', [
            WeekdayEnum::SATURDAY->value,
            WeekdayEnum::SUNDAY->value,
        ])]
        #[StringType]
        #[Regex('/^(\d{1,2}\.\d{1,2})(,\s*\d{1,2}\.\d{1,2})*$/')]
        public ?string $weeklyConsultationDates,
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
                    $semesterId = Semester::getCurrentSemester()->id;

                    $query = ConsultationSemester::where('scientific_worker_id', $userId)
                        ->where('semester_id', $semesterId)
                        ->where(function ($q) use ($startTime, $endTime): void {
                            $q->where('start_time', '<', $endTime)
                                ->where('end_time', '>', $startTime);
                        });

                    if (in_array($value, [WeekdayEnum::MONDAY->value, WeekdayEnum::TUESDAY->value, WeekdayEnum::WEDNESDAY->value, WeekdayEnum::THURSDAY->value, WeekdayEnum::FRIDAY->value])) {
                        $query->where('day', $value)
                            ->where(function ($q): void {
                                $q->where('week_type', WeekTypeEnum::ALL->value)
                                    ->orWhere('week_type', request()->input('dailyConsultationWeekType'));
                            });
                    }

                    // Sprawdzanie konfliktu dla weekendów
                    if (in_array($value, [WeekdayEnum::SATURDAY->value, WeekdayEnum::SUNDAY->value])) {
                        $weeklyDates = $context->fullPayload['weeklyConsultationDates'];

                        if (!empty($weeklyDates)) {
                            $dates = explode(',', $weeklyDates);
                            $trimmedDates = array_map('trim', $dates);

                            $query->where(function ($q) use ($trimmedDates): void {
                                foreach ($trimmedDates as $date) {
                                    $q->orWhere('weekend_consultation_dates', 'LIKE', "%{$date}%");
                                }
                            });
                        }
                    }

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
                    // Sprawdzenie czy czas rozpoczęcia jest w zakresie 7:30-19:30
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
                    // Sprawdzenie czy czas zakończenia jest w zakresie 8:30-20:30
                    $endTime = Carbon::createFromFormat('H:i', $value);
                    $minTime = Carbon::createFromFormat('H:i', '08:30');
                    $maxTime = Carbon::createFromFormat('H:i', '20:30');

                    if ($endTime->lt($minTime) || $endTime->gt($maxTime)) {
                        $fail(__('consultation::consultation.The consultation end time must be between 8:30 and 20:30'));
                    }
                },
                function ($attribute, $value, $fail): void {
                    // Sprawdzanie czy konsultacja nie jest krótsza niż 60 minut
                    $startTimeValue = request()->input('consultationStartTime');

                    if ($startTimeValue) {
                        $startTime = Carbon::createFromFormat('H:i', $startTimeValue);
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
        ];
    }

    public static function messages(): array
    {
        return [
            'consultationWeekday.required' => __('consultation::consultation.Weekday is required'),
            'consultationWeekday.in' => __('consultation::consultation.Invalid weekday selected'),
            'dailyConsultationWeekType.required_if' => __('consultation::consultation.Week type is required for weekday consultations'),
            'dailyConsultationWeekType.in' => __('consultation::consultation.Invalid week type selected'),
            'weeklyConsultationDates.required_if' => __('consultation::consultation.Consultation dates are required for weekend consultations'),
            'weeklyConsultationDates.regex' => __('consultation::consultation.Invalid date format. Use format: DD.MM'),
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
