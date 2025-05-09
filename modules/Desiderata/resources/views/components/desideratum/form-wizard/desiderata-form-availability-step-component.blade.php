<div class="p-4 md:p-6">
    <div class="mb-6">
        <flux:heading size="xl" class="mb-6">{{ __('desiderata::desiderata.Time availability') }}</flux:heading>
        <flux:text class="mb-6">
            <p>{{ __('desiderata::desiderata.Mark up to 5 time slots when you are NOT available') }}</p>
        </flux:text>
        <hr>
    </div>
    
    <!-- Informacja o wybranych slotach -->
    <div class="mb-6 flex justify-between items-center">
        <flux:text color="red">
            <p>{{ __('desiderata::desiderata.Selected') }}: <span class="font-medium">{{ $selectedSlotsCount }}/{{ $maxUnavailableSlots }}</span></p>
        </flux:text>
        
        @if ($selectedSlotsCount >= $maxUnavailableSlots)
            <flux:badge color="rose" aria-live="assertive">{{ __('desiderata::desiderata.Maximum limit reached') }}</flux:badge>
        @endif
    </div>
    
    <!-- Tabela dostępności -->
    <div class="bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg mb-8">
        <flux:legend class="mb-4 text-lg font-semibold" id="availability-legend">
            {{ __('desiderata::desiderata.Availability schedule') }}
        </flux:legend>

        <flux:description class="mb-6">
            {{ __('desiderata::desiderata.Please select time slots when you are NOT available') }}
        </flux:description>

        <div class="overflow-x-auto mb-6">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-zinc-50 dark:bg-zinc-700/50 text-left text-sm font-medium text-zinc-500 dark:text-zinc-400">
                            {{ __('desiderata::desiderata.Time') }}
                        </th>
                        
                        @foreach (\App\Enums\WeekdayEnum::cases() as $day)
                            <th class="py-3 px-4 bg-zinc-50 dark:bg-zinc-700/50 text-center text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                {{ $day->label() }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach (array_keys(reset($unavailableTimeSlots)) as $slotId)
                        <tr class="divide-x divide-zinc-200 dark:divide-zinc-700">
                            <td class="py-3 px-4 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $unavailableTimeSlots[\App\Enums\WeekdayEnum::MONDAY->value][$slotId]['range'] }}
                            </td>
                            
                            @foreach (\App\Enums\WeekdayEnum::cases() as $day)
                                <td class="py-3 px-4 text-center">
                                    <div 
                                        wire:click="toggleTimeSlot('{{ $day->value }}', {{ $slotId }})"
                                        @class([
                                            'cursor-pointer w-6 h-6 mx-auto rounded-full flex items-center justify-center border',
                                            'bg-rose-500 border-rose-600 dark:bg-rose-600 dark:border-rose-700' => $unavailableTimeSlots[$day->value][$slotId]['selected'],
                                            'bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600 hover:bg-zinc-100 dark:hover:bg-zinc-700' => !$unavailableTimeSlots[$day->value][$slotId]['selected'],
                                        ])
                                        aria-labelledby="availability-legend"
                                    >
                                        @if ($unavailableTimeSlots[$day->value][$slotId]['selected'])
                                            <flux:icon name="x-mark" class="h-4 w-4 text-white" aria-hidden="true" />
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg mb-8">
        <flux:legend class="mb-4 text-lg font-semibold" id="additional-info-legend">
            {{ __('desiderata::desiderata.Additional information') }}
        </flux:legend>

        <flux:description class="mb-6">
            {{ __('desiderata::desiderata.Additional information description') }}
        </flux:description>

        <flux:textarea
            id="additional-info"
            name="additional-info"
            rows="4"
            class="w-full"
            aria-labelledby="additional-info-legend"
        />
    </div>

    <div class="flex justify-between items-center">
        <flux:button 
            wire:click="previousStep" 
            variant="ghost"
            class="px-6 py-3"
        >
            <flux:icon name="arrow-left" class="inline-flex h-4 w-4 mr-2" aria-hidden="true" />
            {{ __('desiderata::desiderata.Previous step') }}
        </flux:button>
        
        <flux:button 
            wire:click="saveForm" 
            variant="primary"
            class="px-6 py-3"
        >
            {{ __('desiderata::desiderata.Save preferences') }}
        </flux:button>
    </div>
</div>