<x-common.card.base class="h-full sm:h-auto flex flex-col">
    <div class="p-4 flex flex-col h-full">
        <flux:heading size="lg" level="3" class="mb-1 font-semibold">
            {{ __('consultation::dashboard.Consultations') }}
        </flux:heading>
        @if($semester)
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">
                {{ $semester->name }} {{ $semester->academic_year }}
            </p>
        @endif
        
        <flux:text class="mb-auto text-neutral-600 dark:text-neutral-400">
            <p>{{ __('dashboard.Consultations Description for Scientific Worker') }}</p>
        </flux:text>

        <flux:button wire:navigate href="{{ route('consultations.scientific-worker.my-consultation') }}" variant="primary" class="w-full font-medium py-2.5">
            {{ __('consultation::dashboard.Go to Consultations') }}
        </flux:button>
    </div>
</x-card.base>