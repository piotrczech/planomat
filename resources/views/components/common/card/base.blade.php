@props([
    'title' => '',
    'subtitle' => '',
    'hasPadding' => true,
    'class' => ''
])

<div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 {{ $hasPadding ? 'p-4' : '' }} {{ $class }}">
    @if(!empty($title))
    <div class="{{ !empty($subtitle) ? 'mb-1' : '' }}">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
        @if(!empty($subtitle))
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
        @endif
    </div>
    @endif
    
    <div>
        {{ $slot }}
    </div>
</div> 