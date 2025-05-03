<?php

use function Livewire\Volt\{state};

state([
    'title' => '',
    'description' => '',
    'isRecent' => false
]);

?>

<li class="relative overflow-hidden rounded-md 
    {{ $isRecent 
       ? 'border-l-4 border-blue-500 bg-blue-50/50 dark:border-blue-600 dark:bg-blue-900/10' 
       : 'bg-gray-50 dark:bg-neutral-700/50' }} 
    px-4 py-2.5">
    <div>
        <p class="font-medium text-gray-900 dark:text-white">{{ $title }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
    </div>
</li> 