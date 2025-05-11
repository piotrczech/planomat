<div
    class="p-2"
>
    <flux:heading
        size="xl"
        level="2"
        id="form-heading"
    >
        {{ __('consultation::consultation.New session consultation') }}
    </flux:heading>

    <flux:text class="mb-8">
        <p>
            {{ __('consultation::consultation.New consultation sessions description') }}
        </p>
    </flux:text>

    <div class="bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg mb-8">
        <div class="mb-6">
            <flux:label for="consultation-dates" class="block mb-2 font-medium">
                {{ __('consultation::consultation.Consultation date') }}
            </flux:label>
            
            <div wire:ignore>
                <flux:input
                    x-data
                    x-ref="input"
                    x-init="flatpickr($refs.input, {
                        dateFormat: 'Y-m-d',
                    });"
                    id="consultation-dates"
                    type="text"
                    class="w-full"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <flux:label for="consultation-start-time" class="block mb-2 font-medium">
                    {{ __('consultation::consultation.Consultation start time') }}
                </flux:label>
            
                <div wire:ignore>
                    <flux:input
                        x-data
                        x-init="flatpickr($refs.input, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            minTime: '7:30',
                            maxTime: '19:30'
                        });"
                        x-ref="input"
                        id="consultation-start-time"
                        class="w-full"
                        aria-labelledby="consultation-start-time-legend"
                    />
                </div>
            </div>

            <div>
                <flux:label for="consultation-end-time" class="block mb-2 font-medium">
                    {{ __('consultation::consultation.Consultation end time') }}
                </flux:label>

                <div wire:ignore>
                    <flux:input
                        x-data
                        x-init="flatpickr($refs.input, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            minTime: '8:30',
                            maxTime: '20:30'
                        });"
                        x-ref="input"
                        id="consultation-end-time"
                        class="w-full"
                    />
                </div>
            </div>
        </div>

        <div class="mb-6">
            <flux:label for="consultation-location" class="block mb-2 font-medium">
                {{ __('consultation::consultation.Consultation location') }}
            </flux:label>
            
            <flux:input
                id="consultation-location"
                type="text"
                class="w-full"
                :placeholder="__('consultation::consultation.Consultation location description')"
                aria-labelledby="consultation-location-legend"
            />
        </div>

        <div class="flex justify-end items-center">
            <flux:button
                variant="primary"
                class="px-6 py-3 w-full"
            >
                {{ __('consultation::consultation.Add consultation sessions') }}
            </flux:button>
        </div>
    </div>
</div>
