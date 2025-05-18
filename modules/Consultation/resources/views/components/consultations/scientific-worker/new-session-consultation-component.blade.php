<div
    class="p-2"
    x-data="{ 
        showSuccessAlert: false,
        showErrorAlert: false,
        successMessage: null,
        errorMessage: null
    }"
    x-init="
        $watch('$wire.successMessage', (value) => {
            if (value) {
                showSuccessAlert = true;
                successMessage = value;
                setTimeout(() => { 
                    showSuccessAlert = false;
                    $wire.successMessage = null;
                }, 5000);
            }
        });
        
        $watch('$wire.errorMessage', (value) => {
            if (value) {
                showErrorAlert = true;
                errorMessage = value;
                setTimeout(() => { 
                    showErrorAlert = false;
                    $wire.errorMessage = null;
                }, 5000);
            }
        });
    "
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

    <!-- Komunikat sukcesu -->
    <div x-show="showSuccessAlert" x-transition class="mb-6">
        <flux:callout 
            variant="success" 
            icon="check-circle" 
        >
            <flux:callout.heading>{{ __('consultation::consultation.Success') }}</flux:callout.heading>
            <flux:callout.text x-text="successMessage"></flux:callout.text>

            <x-slot name="controls">
                <flux:button icon="x-mark" variant="ghost" x-on:click="showSuccessAlert = false" />
            </x-slot>
        </flux:callout>
    </div>

    <!-- Ogólny komunikat o błędach (nie walidacji) -->
    <div x-show="showErrorAlert" x-transition class="mb-6">
        <flux:callout 
            variant="danger" 
            icon="exclamation-triangle" 
        >
            <flux:callout.heading>{{ __('consultation::consultation.Error') }}</flux:callout.heading>
            <flux:callout.text x-text="errorMessage"></flux:callout.text>

            <x-slot name="controls">
                <flux:button icon="x-mark" variant="ghost" x-on:click="showErrorAlert = false" />
            </x-slot>
        </flux:callout>
    </div>

    <!-- Ogólny komunikat o błędach walidacji - jak w komponencie semestralnym -->
    @if ($errors->any())
        <div class="mb-6">
            <flux:callout
                variant="danger"
                icon="exclamation-triangle"
            >
                <flux:callout.heading>{{ __('consultation::consultation.Validation errors') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('consultation::consultation.Please correct the errors in the form') }}
                </flux:callout.text>
            </flux:callout>
        </div>
    @endif

    <div class="bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg mb-8">
        <div class="mb-6">
            <flux:label for="consultation-dates" class="block mb-2 font-medium">
                {{ __('consultation::consultation.Consultation date') }}
            </flux:label>
            
            <div
                wire:ignore
                @error('consultationDate')
                    id="consultation-dates-wrapper-error"
                    class="flatpickr-error"
                @enderror
            >
                <flux:input
                    x-data
                    x-ref="input"
                    x-init="flatpickr($refs.input, {
                        dateFormat: 'Y-m-d',
                        onChange: function(selectedDates, dateStr, instance) {
                            $wire.consultationDate = dateStr;
                        }
                    });"
                    id="consultation-dates"
                    type="text"
                    class="w-full"
                />
            </div>

            @error('consultationDate')
                <flux:text class="text-red-500 dark:text-red-400 mt-2 text-sm">
                    {{ $message }}
                </flux:text>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <flux:label for="consultation-start-time" class="block mb-2 font-medium">
                    {{ __('consultation::consultation.Consultation start time') }}
                </flux:label>
            
                <div
                    wire:ignore
                    @error('consultationStartTime')
                    id="consultation-start-time-wrapper-error"
                    class="flatpickr-error"
                    @enderror
                >
                    <flux:input
                        x-data
                        x-init="flatpickr($refs.input, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            minTime: '7:30',
                            maxTime: '19:30',
                            onChange: function(selectedDates, dateStr, instance) {
                                $wire.consultationStartTime = dateStr;
                            }
                        });"
                        x-ref="input"
                        id="consultation-start-time"
                        class="w-full {{ $errors->has('consultationStartTime') ? 'border-red-500 dark:border-red-400' : '' }}"
                        aria-labelledby="consultation-start-time-legend"
                    />
                </div>

                @error('consultationStartTime')
                    <flux:text class="text-red-500 dark:text-red-400 mt-2 text-sm">
                        {{ $message }}
                    </flux:text>
                @enderror
            </div>

            <div>
                <flux:label for="consultation-end-time" class="block mb-2 font-medium">
                    {{ __('consultation::consultation.Consultation end time') }}
                </flux:label>

                <div
                    wire:ignore
                    @error('consultationEndTime')
                    id="consultation-end-time-wrapper-error"
                    class="flatpickr-error"
                    @enderror
                >
                    <flux:input
                        x-data
                        x-init="flatpickr($refs.input, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            minTime: '8:30',
                            maxTime: '20:30',
                            onChange: function(selectedDates, dateStr, instance) {
                                $wire.consultationEndTime = dateStr;
                            }
                        });"
                        x-ref="input"
                        id="consultation-end-time"
                        class="w-full {{ $errors->has('consultationEndTime') ? 'border-red-500 dark:border-red-400' : '' }}"
                    />
                </div>

                @error('consultationEndTime')
                    <flux:text class="text-red-500 dark:text-red-400 mt-2 text-sm">
                        {{ $message }}
                    </flux:text>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <flux:label for="consultation-location" class="block mb-2 font-medium">
                {{ __('consultation::consultation.Consultation location') }}
            </flux:label>
            
            <flux:input
                id="consultation-location"
                type="text"
                class="w-full {{ $errors->has('consultationLocation') ? 'border-red-500 dark:border-red-400' : '' }}"
                :placeholder="__('consultation::consultation.Consultation location description')"
                aria-labelledby="consultation-location-legend"
                wire:model="consultationLocation"
            />

            @error('consultationLocation')
                <flux:text class="text-red-500 dark:text-red-400 mt-2 text-sm">
                    {{ $message }}
                </flux:text>
            @enderror
        </div>

        <div class="flex justify-end items-center">
            <flux:button
                variant="primary"
                class="px-6 py-3 w-full"
                wire:click="addConsultation"
                wire:loading.attr="disabled"
                wire:target="addConsultation"
            >
                <span wire:loading.remove wire:target="addConsultation">
                    {{ __('consultation::consultation.Add consultation sessions') }}
                </span>
                <span wire:loading wire:target="addConsultation">
                    {{ __('consultation::consultation.Adding...') }}
                </span>
            </flux:button>
        </div>
    </div>
</div>
