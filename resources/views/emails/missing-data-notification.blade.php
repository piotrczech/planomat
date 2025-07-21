<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('notifications.missing_data_subject') }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; line-height: 1.6; color: #3d4852; background-color: #f5f8fa; margin: 0; padding: 20px; }
        .container { max-width: 680px; margin: 0 auto; padding: 25px; border-radius: 8px; background-color: #ffffff; border: 1px solid #e8e5ef; }
        .header { text-align: center; border-bottom: 1px solid #e8e5ef; padding-bottom: 20px; margin-bottom: 25px; }
        .header h1 { font-size: 24px; color: #2f3943; font-weight: 600; }
        .header .plano-mat { color: #a89b56; font-weight: 700; }
        .content { padding: 10px 0; }
        .content p { margin: 0 0 15px; }
        .highlight { background-color: #a89b561a; border-left: 4px solid #a89b56; padding: 15px 20px; margin: 20px 0; }
        .button-container { text-align: center; margin: 30px 0; }
        .button { background-color: #a89b56; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: 600; display: inline-block; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Plano<span class="plano-mat">MAT</span></h1>
        </div>

        <div class="content">
            <h2>{{ __('notifications.missing_data_title') }}</h2>

            <p>{{ __('notifications.app_intro') }}</p>

            <div class="highlight">
                @php
                    $semester = $notificationDto->semester;
                    $academicYear = $semester->academic_year;
                    $season = $semester->season;
                    $seasonEnum = \App\Domain\Enums\SemesterSeasonEnum::WINTER;
                @endphp
                @switch($notificationDto->type)
                    @case('semester_consultations')
                        @php
                            $seasonLabel = $season === $seasonEnum
                                ? __('consultation::consultation.in_semester_winter')
                                : __('consultation::consultation.in_semester_summer');
                            $semesterName = $seasonLabel . ' ' . $academicYear;
                        @endphp
                        <p>{{ __('notifications.missing_semester_consultations', ['semesterName' => $semesterName]) }}</p>
                        @break
                    @case('session_consultations')
                        @php
                            $seasonLabel = $season === $seasonEnum
                                ? __('consultation::consultation.in_session_winter')
                                : __('consultation::consultation.in_session_summer');
                            $semesterName = $seasonLabel . ' ' . $academicYear;
                        @endphp
                        <p>{{ __('notifications.missing_session_consultations', ['semesterName' => $semesterName]) }}</p>
                        @break
                    @case('desiderata')
                        @php
                            $seasonLabel = $season === $seasonEnum
                                ? __('consultation::consultation.in_semester_winter')
                                : __('consultation::consultation.in_semester_summer');
                            $semesterName = $seasonLabel . ' ' . $academicYear;
                        @endphp
                        <p>{{ __('notifications.missing_desiderata', ['semesterName' => $semesterName]) }}</p>
                        @break
                @endswitch
            </div>

            <p>{{ __('notifications.deadline_info') }}</p>
        </div>

        <div class="button-container">
            <a href="{{ config('app.url') }}" class="button">{{ __('notifications.go_to_app') }}</a>
        </div>

        <div class="footer">
            <p>{{ __('notifications.automated_notification') }}</p>
        </div>
    </div>
</body>
</html> 