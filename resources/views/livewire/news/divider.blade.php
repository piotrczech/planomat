<?php

use function Livewire\Volt\{state};

state(['label' => 'dashboard.Earlier']);

?>

<li class="my-2">
    <div class="relative flex items-center py-1">
        <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
        <span class="mx-2 flex-shrink text-xs text-gray-500 dark:text-gray-400">{{ __($label) }}</span>
        <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
    </div>
</li> 