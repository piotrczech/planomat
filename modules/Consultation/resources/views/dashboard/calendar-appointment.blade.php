@php
use App\Enums\WeekTypeEnum;
@endphp

<div class="rounded-md bg-blue-100 px-1 py-2 dark:bg-blue-900/30">
    <flux:text>
        <p>
            {{ $time }}
            @if($weekType !== WeekTypeEnum::ALL)
                ({{ $weekType->shortLabel() }})
            @endif
        </p>
    </flux:text>
    <flux:text>
        <p>
            {{ $location }}
        </p>
    </flux:text>
</div> 