@php
    $startDate = \Carbon\Carbon::parse($summaryDto->weekStart)->locale('pl_PL');
    $endDate = \Carbon\Carbon::parse($summaryDto->weekEnd)->locale('pl_PL');

    $formattedDate = '';
    if ($startDate->year !== $endDate->year) {
        $formattedDate = $startDate->translatedFormat('j F Y') . ' - ' . $endDate->translatedFormat('j F Y');
    } elseif ($startDate->month !== $endDate->month) {
        $formattedDate = $startDate->translatedFormat('j F') . ' - ' . $endDate->translatedFormat('j F Y');
    } else {
        $formattedDate = $startDate->translatedFormat('j') . '-' . $endDate->translatedFormat('j F Y');
    }
@endphp

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('notifications.weekly_summary_subject') }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; line-height: 1.6; color: #3d4852; background-color: #f5f8fa; margin: 0; padding: 20px; }
        .container { max-width: 680px; margin: 0 auto; padding: 25px; border-radius: 8px; background-color: #ffffff; border: 1px solid #e8e5ef; }
        .header { text-align: center; border-bottom: 1px solid #e8e5ef; padding-bottom: 20px; margin-bottom: 25px; }
        .header h1 { font-size: 24px; color: #2f3943; font-weight: 600; }
        .header .plano-mat { color: #a89b56; font-weight: 700; }
        .summary-tiles { margin-bottom: 30px; width: 100%; }
        .summary-tiles table { width: 100%; border-collapse: separate; border-spacing: 20px 0; }
        .tile { padding: 20px; border-radius: 8px; text-align: center; background-color: #a89b56; color: white; width: 50%; }
        .tile .label { font-size: 16px; margin-bottom: 8px; opacity: 0.9; }
        .tile .count { font-size: 32px; font-weight: bold; }
        .content-section h2 { font-size: 20px; color: #2f3943; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #e8e5ef; padding-bottom: 10px;}
        .user-list { list-style-type: none; padding: 0; margin: 0; }
        .user-list.multi-column { columns: 2; -webkit-columns: 2; -moz-columns: 2; column-gap: 40px; }
        .user-list li { padding: 8px 0; border-bottom: 1px solid #ecf0f1; font-size: 15px; }
        .user-list.multi-column li { break-inside: avoid-column; }
        .user-list li:last-child { border-bottom: none; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #7f8c8d; }
        .footer a { color: #a89b56; text-decoration: none; }
        .warning-box { background-color: #fffbe6; border: 1px solid #ffe58f; border-radius: 8px; padding: 15px; margin: 25px 0; }
        .warning-box h2 { margin-top: 0; font-size: 18px; color: #d46b08; border: none; }
        .warning-box ul { padding-left: 20px; margin: 0; }
        .warning-box li { color: #874d00; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Plano<span class="plano-mat">MAT</span>: {{ __('notifications.weekly_summary_title') }}</h1>
            <p>{{ __('notifications.weekly_period', ['period' => $formattedDate]) }}</p>
        </div>

        @if(!$summaryDto->isConsultationSemesterActive || !$summaryDto->isDesiderataSemesterActive)
            <div class="warning-box">
                <h2>{{ __('notifications.important_notes') }}</h2>
                <ul>
                    @if(!$summaryDto->isConsultationSemesterActive)
                        <li>{{ __('notifications.no_active_consultation_semester') }}</li>
                    @endif
                    @if(!$summaryDto->isDesiderataSemesterActive)
                        <li>{{ __('notifications.no_active_desiderata_semester') }}</li>
                    @endif
                </ul>
            </div>
        @endif

        <div class="summary-tiles">
            <table>
                <tr>
                    <td class="tile">
                        <div class="label">{{ __('notifications.consultation_changes') }}</div>
                        <div class="count">{{ $summaryDto->generalActivity['consultation_changes'] }}</div>
                    </td>
                    <td class="tile">
                        <div class="label">{{ __('notifications.desiderata_changes') }}</div>
                        <div class="count">{{ $summaryDto->generalActivity['desiderata_changes'] }}</div>
                    </td>
                </tr>
            </table>
        </div>

        @if($summaryDto->generalActivity['consultation_changes'] > 0)
            <div class="content-section">
                <h2>{{ __('notifications.users_who_changed_consultations') }}</h2>
                <ul class="user-list {{ count($summaryDto->consultationsActivity) > 10 ? 'multi-column' : '' }}">
                    @forelse($summaryDto->consultationsActivity as $activity)
                        <li>{{ $activity }}</li>
                    @empty
                        <li>{{ __('notifications.no_consultation_activity') }}</li>
                    @endforelse
                </ul>
            </div>
        @endif

        @if($summaryDto->generalActivity['desiderata_changes'] > 0)
            <div class="content-section">
                <h2>{{ __('notifications.users_who_changed_desiderata') }}</h2>
                <ul class="user-list {{ count($summaryDto->desiderataActivity) > 10 ? 'multi-column' : '' }}">
                    @forelse($summaryDto->desiderataActivity as $activity)
                        <li>{{ $activity }}</li>
                    @empty
                        <li>{{ __('notifications.no_desiderata_activity') }}</li>
                    @endforelse
                </ul>
            </div>
        @endif

        <div class="footer">
            <p><a href="{{ config('app.url') }}">{{ __('notifications.go_to_app') }}</a></p>
            <p>
                {{ __('notifications.automated_notification') }}<br>
                {{ __('notifications.only_from_pwr') }}
            </p>
        </div>
    </div>
</body>
</html> 