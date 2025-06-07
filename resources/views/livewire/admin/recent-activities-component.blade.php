<div>
    <div class="bg-white dark:bg-neutral-800/70 shadow-lg sm:rounded-xl overflow-hidden">
        <div class="p-3 sm:p-4 border-b border-neutral-200 dark:border-neutral-700">
            <flux:text size="xs" class="text-neutral-500 dark:text-neutral-400 font-bold">
                {{ __('dashboard.Activities last 2 weeks scrollable') }}
            </flux:text>
        </div>
        <ul class="divide-y divide-neutral-200 dark:divide-neutral-700 max-h-96 overflow-y-auto">
            @forelse ($activities as $activity)
                <li class="p-3 sm:p-4 hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                    <div class="flex items-center justify-between space-x-3">
                        <div class="min-w-0 flex-1">
                            <flux:text>
                                {{ $this->formatActivityMessage($activity) }}
                            </flux:text>
                        </div>
                        <div class="text-right flex-shrink-0 space-x-2">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400" title="{{ $activity->created_at->format('Y-m-d H:i:s') }}">
                                {{ $activity->created_at->diffForHumans() }}
                            </span>
                            @php
                                $moduleEnum = App\Enums\ActivityLogModuleEnum::from($activity->module);
                                $color = match ($moduleEnum) {
                                    App\Enums\ActivityLogModuleEnum::CONSULTATION => 'blue',
                                    App\Enums\ActivityLogModuleEnum::DESIDERATA => 'green',
                                    default => 'gray',
                                };
                            @endphp
                            <flux:badge color="{{ $color }}" variant="outline" size="sm">
                                {{ $moduleEnum->label() }}
                            </flux:badge>
                        </div>
                    </div>
                </li>
            @empty
                <li class="p-3 sm:p-4">
                    <flux:text class="text-neutral-500 dark:text-neutral-400 text-center">
                        {{ __('dashboard.No recent activities') }}
                    </flux:text>
                </li>
            @endforelse
        </ul>
    </div>
</div> 