<div
    class="p-2"
    x-data="{
        isInitialized: false,
        isWeekdaySelected: false,
        selectedWeekday: null,
        consultationDaysPickerInstance: null,

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

    <div class="bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg mb-8">
        <div class="mb-6">
            <flux:label for="consultation-weekday" class="block mb-2 font-medium">
                {{ __('consultation::consultation.Consultation weekday') }}
            </flux:label>

            <select 
                id="consultation-weekday" 
                class="w-full"
                aria-labelledby="consultation-weekday-legend"
            >
                @foreach(\App\Enums\WeekdayEnum::cases() as $weekday)
                    <option value="{{ $weekday->value }}">
                        {{ $weekday->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- for pon-pt --}}
        <div class="mb-6" x-show="isWeekdaySelected">
            <flux:label for="consultation-week-type" class="block mb-2 font-medium">
                {{ __('consultation::consultation.Consultation week type') }}
            </flux:label>

            <select
                id="consultation-week-type"
                class="w-full"
            >
                <option value="every">{{ __('consultation::consultation.Every week') }}</option>
                <option value="even">{{ __('consultation::consultation.Even weeks') }}</option>
                <option value="odd">{{ __('consultation::consultation.Odd weeks') }}</option>
            </select>
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
                            maxTime: '19:30',
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
                            maxTime: '20:30',
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
                class="px-6 py-3"
            >
                {{ __('consultation::consultation.Add consultation') }}
            </flux:button>
        </div>
    </div>
</div>
