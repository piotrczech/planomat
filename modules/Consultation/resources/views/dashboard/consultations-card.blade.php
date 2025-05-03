<x-common.card.base class="h-full sm:h-auto flex flex-col">
    <div class="flex justify-between items-center mb-3">
        <flux:heading size="lg" level="2">
            {{ __('consultation::dashboard.Consultations') }}
        </flux:heading>
        <flux:button 
            wire:click="redirectToForm"
            size="sm"
        >
            {{ __('consultation::dashboard.Edit') }}
        </flux:button>
    </div>

    <flux:text class="mb-3">
        <p>{{ __('consultation::dashboard.Your office hours') }}</p>
    </flux:text>
    
    <div class="flex-grow overflow-y-auto space-y-3 max-h-[400px] sm:max-h-[250px] md:max-h-[300px] lg:max-h-[250px]">
        @foreach($consultations as $consultation)
            <livewire:consultation::dashboard.consultation-item
                :consultation="$consultation"
            />    
        @endforeach
    </div>
</x-card.base>