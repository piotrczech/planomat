<x-common.card.base class="h-full sm:h-auto flex flex-col">
    <div class="p-4 flex flex-col h-full">
        <flux:heading size="lg" level="3" class="mb-4 font-semibold">
            {{ __('desiderata::dashboard.Desiderata') }}
        </flux:heading>
        
        <flux:text class="mb-auto text-neutral-600 dark:text-neutral-400">
            <p>{{ __('desiderata::dashboard.Desiderata Description') }}</p>
            {{-- Tutaj można dodać więcej informacji lub statystyk dotyczących dezyderatów --}}
            {{-- Np. Liczba złożonych dezyderatów, termin składania etc. --}}
        </flux:text>
        
        {{-- TODO: Zmienić `route` na właściwy dla widoku/formularza dezyderatów --}}
        <a href="#" wire:navigate class="mt-6">
            <flux:button variant="primary" class="w-full font-medium py-2.5">
                {{ __('desiderata::dashboard.Go to Desiderata') }}
            </flux:button>
        </a>
    </div>
</x-common.card.base>