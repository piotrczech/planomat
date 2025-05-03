<?php

use App\Enums\WeekTypeEnum;
?>

<div class="rounded-md bg-gray-50 px-3 py-2 dark:bg-neutral-700/50">
    <div class="flex items-center text-sm">
        <flux:icon name="clock" class="mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" />
        <flux:text>
            <p class="text-gray-700 dark:text-gray-300">
                {{ $consultation['day'] }}: {{ $consultation['time'] }}

                @if($consultation['week_type'] !== WeekTypeEnum::ALL)
                    <span class="ml-1 text-blue-600 dark:text-blue-400">
                        ({{ $consultation['week_type']->shortLabel() }})
                    </span>
                @endif
            </p>
        </flux:text>
    </div>
    <div class="mt-1 flex items-center text-sm">
        <flux:icon name="map-pin" class="mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" />
        <span class="text-gray-700 dark:text-gray-300">{{ $consultation['location'] }}</span>
    </div>
</div> 