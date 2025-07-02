<x-common.card.base class="h-full sm:h-auto flex flex-col">
    <div class="p-4 flex flex-col h-full">
        <flux:heading size="lg" level="3" class="mb-1 font-semibold">
            {{ __('desiderata::dashboard.Desiderata') }}
        </flux:heading>
        @if($semester)
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">
                {{ $semester->name }} {{ $semester->academic_year }}
            </p>
        @endif
        
        <flux:text class="mb-auto text-neutral-600 dark:text-neutral-400">
            <p>{{ __('desiderata::dashboard.Desiderata Description for Scientific Worker') }}</p>
        </flux:text>

        <flux:button wire:navigate href="{{ route('desiderata.scientific-worker.my-desiderata') }}" variant="primary" class="w-full font-medium py-2.5">
            {{ __('desiderata::dashboard.Go to Desiderata') }}
        </flux:button>
    </div>
</x-common.card.base>