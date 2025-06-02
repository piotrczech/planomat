<div
    class="p-2"
    x-data="{
        isInitialized: false,
        isWeekdaySelected: false,
        selectedWeekday: null,
        consultationDaysPickerInstance: null,
        showSuccessAlert: false,
        showErrorAlert: false,
        errorMessage: '',
        successMessage: '',

        get consultationDaysPickerOptions() {
            return {
                dateFormat: 'd.m',
                mode: 'multiple',
                locale: 'pl',
                disable: [
                    (date) => {
                        // Domyślnie blokuj wszystkie dni poza weekendem
                        if (!(date.getDay() === 0 || date.getDay() === 6)) {
                            return true;
                        }
                        
                        // Jeśli wybrano sobotę, odblokuj tylko soboty
                        if (this.selectedWeekday === 'saturday') {
                            return date.getDay() !== 6;
                        }
                        
                        // Jeśli wybrano niedzielę, odblokuj tylko niedziele
                        if (this.selectedWeekday === 'sunday') {
                            return date.getDay() !== 0;
                        }
                        
                        // Dla pozostałych przypadków pozwól na oba dni weekendu
                        return false;
                    }
                ]
            };
        },
        
        updateFlatpickr() {
            if (this.consultationDaysPickerInstance) {
                // Usuń poprzednią instancję
                this.consultationDaysPickerInstance.destroy();
                
                // Wyczyść wartość inputa
                this.$refs.datesInput.value = '';
                
                // Utwórz nową instancję z aktualnymi opcjami
                this.consultationDaysPickerInstance = flatpickr(
                    this.$refs.datesInput, 
                    this.consultationDaysPickerOptions
                );
            }
        },
        
        initializeDatePicker() {
            this.consultationDaysPickerInstance = flatpickr(
                this.$refs.datesInput, 
                this.consultationDaysPickerOptions
            );
        }
    }"
    x-init="
        const updateSelectedWeekday = (day) => {
            selectedWeekday = day;
            isWeekdaySelected = day !== 'saturday' && day !== 'sunday';
            
            // Po zmianie dnia tygodnia zaktualizuj flatpickr
            $nextTick(() => updateFlatpickr());
        }

        const weekdaySelect = new TomSelect('#consultation-weekday', { create: false, searchField: [], controlInput: null });
        new TomSelect('#consultation-week-type', { create: false, searchField: [], controlInput: null });

        weekdaySelect.on('change', (value) => updateSelectedWeekday(value));
        updateSelectedWeekday(weekdaySelect.getValue());
        
        // Inicjalizacja pickera po załadowaniu strony
        $nextTick(() => initializeDatePicker());

        // Nasłuchiwanie eventów
        $wire.on('consultationSaved', (data) => {
            showSuccessAlert = true;
            successMessage = '{{ __('consultation::consultation.Successfully created') }}';
            window.scrollTo(0, 0);
            setTimeout(() => { showSuccessAlert = false; }, 5000);
        });
    "
>
    <flux:heading
        size="xl"
        level="2"
        id="form-heading"
    >
        {{ __('consultation::consultation.New semester consultation') }}
    </flux:heading>

    <flux:text class="mb-8">
        <p>
            {{ __('consultation::consultation.New semester consultation description') }}
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

    <!-- Ogólny komunikat o błędach -->
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
            <flux:label for="consultation-weekday" class="block mb-2 font-medium">
                {{ __('consultation::consultation.Consultation weekday') }}
            </flux:label>

            <div wire:ignore>
                <select 
                    id="consultation-weekday" 
                    aria-labelledby="consultation-weekday-legend"
                    wire:model="consultationWeekday"
                >
                    @foreach(\App\Enums\WeekdayEnum::cases() as $weekday)
                        <option value="{{ $weekday->value }}">
                            {{ $weekday->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            @error('consultationWeekday')
                <flux:text class="text-red-500 dark:text-red-400 mt-2 text-sm">
                    {{ $message }}
                </flux:text>
            @enderror
        </div>

        {{-- for pon-pt --}}
        <div class="mb-6" x-show="isWeekdaySelected">
            <flux:label for="consultation-week-type" class="block mb-2 font-medium">
                {{ __('consultation::consultation.Consultation week type') }}
            </flux:label>

            <div wire:ignore>
                <select
                    id="consultation-week-type"
                    class="w-full {{ $errors->has('dailyConsultationWeekType') ? 'border-red-500 dark:border-red-400' : '' }}"
                    wire:model="dailyConsultationWeekType"
                >
                    @foreach(\App\Enums\WeekTypeEnum::cases() as $weekType)
                        <option value="{{ $weekType->value }}">
                            {{ $weekType->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            @error('dailyConsultationWeekType')
                <flux:text class="text-red-500 dark:text-red-400 mt-2 text-sm">
                    {{ $message }}
                </flux:text>
            @enderror
        </div>

        {{-- for sob-ndz --}}
        <div class="mb-6" x-show="!isWeekdaySelected">
            <flux:label for="consultation-dates" class="block mb-2 font-medium">
                {{ __('consultation::consultation.Consultation dates') }}
            </flux:label>
            
            <div wire:ignore>
                <flux:input
                    x-ref="datesInput"
                    type="text"
                    class="w-full {{ $errors->has('weeklyConsultationDates') ? 'border-red-500 dark:border-red-400' : '' }}"
                    wire:model="weeklyConsultationDates"
                />
            </div>

            @error('weeklyConsultationDates')
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
                        class="w-full"
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
                        class="w-full"
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
                class="px-6 py-3"
                wire:click="addConsultation"
                wire:loading.attr="disabled"
                wire:target="addConsultation"
            >
                <span wire:loading.remove wire:target="addConsultation">
                    {{ __('consultation::consultation.Add consultation') }}
                </span>
                <span wire:loading wire:target="addConsultation">
                    {{ __('consultation::consultation.Adding...') }}
                </span>
            </flux:button>
        </div>
    </div>
</div>
