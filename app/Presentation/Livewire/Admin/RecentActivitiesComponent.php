<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Application\UseCases\ActivityLog\GetActivityLogsByLastDaysUseCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use App\Infrastructure\Models\ActivityLog;

class RecentActivitiesComponent extends Component
{
    public Collection $activities;

    public function mount(GetActivityLogsByLastDaysUseCase $getActivityLogsByLastDaysUseCase): void
    {
        $this->activities = $getActivityLogsByLastDaysUseCase->execute();
    }

    public function render(): View
    {
        return view('livewire.admin.recent-activities-component');
    }

    public function formatActivityMessage(ActivityLog $activity): string
    {
        $userName = $activity->user?->fullName() ?? __('dashboard.Unknown User');

        if (app()->getLocale() !== 'pl') {
            $action = __('activity_log.action.' . $activity->action);
            $module = __('activity_log.module_accusative.' . $activity->module);

            return "{$userName} {$action} {$module}.";
        }

        $nameParts = explode(' ', $userName);
        $firstName = $nameParts[0];
        $isFemale = mb_substr($firstName, -1) === 'a' && $firstName !== 'Barnaba';

        $actionKey = 'action_' . ($isFemale ? 'female' : 'male') . '.' . $activity->action;
        $moduleKey = 'module_accusative.' . $activity->module;

        $actionVerb = __('activity_log.' . $actionKey);
        $moduleNoun = __('activity_log.' . $moduleKey);

        return "{$userName} {$actionVerb} {$moduleNoun}.";
    }
}
