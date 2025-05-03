<x-common.card.base class="h-full flex flex-col">
    <div class="flex justify-between items-center mb-3">
        <flux:heading size="lg" level="2">
            {{ __('desiderata::dashboard.Desiderata') }}
        </flux:heading>

        @if($this->wasFormSubmitted)
            <flux:badge color="emerald" class="text-sm">
                {{ __('desiderata::dashboard.Form submitted') }}
            </flux:badge>
        @else
            <flux:badge class="text-sm">
                {{ __('desiderata::dashboard.Remaining: :days days', ['days' => $this->remainingDays]) }}
            </flux:badge>
        @endif
    </div>
    
    <flux:text class="mb-3">
        <p>
            {{ __('desiderata::dashboard.Desiderata are a set of preferences that you can set for your teaching preferences. They are used to help you find the best possible teaching options.') }}
        </p>
    </flux:text>

    @if($this->remainingDays > 0)
        <div>
            <flux:button 
                variant="primary"
                wire:click="redirectToForm"
            >
                {{
                    $this->wasFormSubmitted
                    ? __('desiderata::dashboard.Edit form')
                    : __('desiderata::dashboard.Complete form')
                }}
            </flux:button>
        </div>
    @endif
</x-card.base>