<x-common.card.base class="h-full sm:h-auto flex flex-col">
    <div class="p-4 flex flex-col h-full">
        <flux:heading size="lg" level="3" class="mb-4 font-semibold">
            {{ __('consultation::dashboard.Consultations') }}
        </flux:heading>
        
        <flux:text class="mb-auto text-neutral-600 dark:text-neutral-400">
            <p>{{ __('dashboard.Consultations Description') }}</p>
        </flux:text>
        
        <a href="#" wire:navigate class="mt-6">
            <flux:button variant="primary" class="w-full font-medium py-2.5">
                {{ __('dashboard.Go to Consultations') }}
            </flux:button>
        </a>
    </div>
</x-card.base>