<x-common.card.base class="h-full flex flex-col">
    <div class="p-4 flex flex-col h-full">
        <flux:heading size="lg" level="3" class="mb-4 font-semibold">
            {{ __('desiderata::dashboard.Desiderata') }}
        </flux:heading>
        
        <flux:text class="mb-auto text-neutral-600 dark:text-neutral-400">
            <p>{{ __('dashboard.Desiderata Description') }}</p>
        </flux:text>
        
        <a href="{{ route('desiderata.scientific-worker.my-desiderata') }}" wire:navigate class="mt-6">
            <flux:button variant="primary" class="w-full font-medium py-2.5">
                {{ __('dashboard.Go to Desiderata') }}
            </flux:button>
        </a>
    </div>
</x-card.base>