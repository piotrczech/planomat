<div
    x-data="{ 
        showSuccessAlert: false,
        showErrorAlert: false,
        successMessage: null,
        errorMessage: null,
        showConsultationDetails: false, 
        showNoConsultationMessage: false,
        selectedDay: null,
        noConsultationDate: null,
        consultationsForDay: []
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
        {{ __('consultation::consultation.My Part-time Consultation') }}
    </flux:heading>

    <flux:text class="mb-6">
        <p>
            {{ __('consultation::consultation.My part-time consultation description') }}
        </p>
    </flux:text>

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

    <div class="dark:bg-zinc-900 rounded-lg">
        <div class="flex justify-between items-center mb-4 bg-zinc-100 dark:bg-zinc-800 p-3 rounded-lg">
            <div class="flex items-center">
                <flux:heading
                    size="lg"
                    level="3"
                    class="text-zinc-700 dark:text-zinc-200 mb-0"
                >
                    {{ $currentMonth->translatedFormat('F Y') }}
                </flux:heading>
            </div>
            <div class="flex gap-2">
                <flux:button
                    wire:click="changeMonth(-1)"
                    variant="outline"
                    size="sm"
                    icon="chevron-left"
                    class="bg-white dark:bg-zinc-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/50"
                    :disabled="$this->isFirstMonth()"
                />
                <flux:button
                    wire:click="changeMonth(1)"
                    variant="outline"
                    size="sm"
                    icon="chevron-right"
                    class="bg-white dark:bg-zinc-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/50"
                    :disabled="$this->isLastMonth()"
                />
            </div>
        </div>

        <div class="grid grid-cols-7 gap-1.5">
            @foreach(['Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'Sb', 'Nd'] as $index => $dayName)
                <div class="text-center font-medium py-2 px-1 bg-zinc-100 dark:bg-zinc-800 rounded-t-md {{ in_array($index, [5, 6]) ? 'text-red-600 dark:text-red-400 font-bold' : 'text-zinc-700 dark:text-zinc-300' }}">
                    {{ $dayName }}
                </div>
            @endforeach

            @foreach($calendarDays as $day)
                @php
                    $isDisabled = !$day['isInRange'] || !$day['isCurrentMonth'];
                    $isWeekend = in_array($day['dayOfWeek'], [6, 0]); // 6=Saturday, 0=Sunday
                    $consultationsForDay = collect($consultationEvents)
                        ->filter(fn($event) => $event['consultation_date'] === $day['date'])
                        ->toArray();
                    $hasConsultations = count($consultationsForDay) > 0;
                @endphp
                <div wire:key="day-{{ $day['date'] }}" 
                    class="aspect-square {{ !$day['isCurrentMonth'] ? 'opacity-40' : '' }} 
                           {{ !$day['isInRange'] ? 'opacity-30' : '' }}">
                    @if($day['isCurrentMonth'])
                        <button 
                            class="w-full h-full flex flex-col {{ $isDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }}"
                            @if($hasConsultations && !$isDisabled)
                                @click="
                                    showConsultationDetails = true; 
                                    showNoConsultationMessage = false;
                                    selectedDay = '{{ $day['date'] }}';
                                    consultationsForDay = @js($consultationsForDay);
                                "
                            @elseif(!$isDisabled)
                                @click="
                                    showNoConsultationMessage = true;
                                    showConsultationDetails = false;
                                    noConsultationDate = '{{ $day['date'] }}';
                                "
                            @endif
                            {{ $isDisabled ? 'disabled' : '' }}
                        >
                            <div class="p-1 {{ $isWeekend ? 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200' }} 
                                      {{ $day['isToday'] ? 'bg-indigo-200 dark:bg-indigo-800/70 text-indigo-800 dark:text-indigo-100' : '' }} 
                                      {{ $hasConsultations ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-100' : '' }}
                                      font-bold text-center">
                                {{ $day['day'] }}
                            </div>
                            
                            <!-- Zawartość dnia -->
                            <div class="flex-grow p-1 {{ $hasConsultations ? 'bg-indigo-50 dark:bg-indigo-900/20' : 'bg-white dark:bg-zinc-900' }} 
                                      {{ $isWeekend ? 'border-red-200 dark:border-red-800/70' : 'border-zinc-200 dark:border-zinc-700/70' }}
                                      border-x border-b">
                                @if($hasConsultations && !$isDisabled)
                                    <div class="text-center text-xs font-medium text-indigo-700 dark:text-indigo-200">
                                        {{ count($consultationsForDay) }} 
                                        @if(count($consultationsForDay) == 1)
                                            {{ __('consultation::consultation.consultation_singular') }}
                                        @else
                                            {{ __('consultation::consultation.consultations_plural') }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </button>
                    @else
                        <div class="w-full h-full bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800/50">
                            <div class="p-1 bg-zinc-100 dark:bg-zinc-800/80 text-zinc-400 dark:text-zinc-500 font-bold text-center">
                                {{ $day['day'] }}
                            </div>
                            <div class="flex-grow p-1 dark:bg-zinc-900/60"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div 
            x-show="showConsultationDetails" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
        >
            <div class="flex items-center justify-center min-h-screen px-4">
                <div 
                    class="fixed inset-0 bg-black opacity-30"
                    @click="showConsultationDetails = false"
                ></div>
                
                <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl mx-auto max-w-lg w-full p-6">
                    <div class="flex justify-between items-start mb-4">
                        <flux:heading
                            size="lg"
                            level="3"
                            class="dark:text-zinc-100"
                            x-text="selectedDay ? new Date(selectedDay).toLocaleDateString('pl-PL', {day: 'numeric', month: 'long', year: 'numeric'}) : ''"
                        />
                        <flux:button
                            @click="showConsultationDetails = false"
                            variant="ghost"
                            size="sm"
                            icon="x-mark"
                            sr-text="{{ __('consultation::consultation.Close') }}"
                        />
                    </div>
                    
                    <div class="space-y-3">
                        <template x-for="(consultation, index) in consultationsForDay" :key="index">
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3 dark:bg-zinc-850">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center">
                                            <flux:icon 
                                                name="clock" 
                                                class="h-4 w-4 text-zinc-500 dark:text-zinc-400 mr-1.5" 
                                            />
                                            <span class="font-medium text-zinc-800 dark:text-zinc-100" x-text="`${consultation.start_time} - ${consultation.end_time}`"></span>
                                        </div>
                                        
                                        <div class="flex items-center mt-1">
                                            <flux:icon 
                                                name="map-pin" 
                                                class="h-4 w-4 text-zinc-500 dark:text-zinc-400 mr-1.5" 
                                            />
                                            <span class="text-sm text-zinc-600 dark:text-zinc-300" x-text="consultation.locationBuilding"></span>
                                            <span x-show="consultation.locationRoom">,&nbsp;</span>
                                            <span class="text-sm text-zinc-600 dark:text-zinc-300" x-text="consultation.locationRoom"></span>
                                        </div>
                                    </div>
                                    
                                    <flux:button
                                        @click="$wire.removeConsultation(consultation.id).then(() => { 
                                            showConsultationDetails = false;
                                            showSuccessAlert = true;
                                            successMessage = '{{ __('consultation::consultation.Consultation successfully deleted') }}';
                                            setTimeout(() => { showSuccessAlert = false }, 5000);
                                        }).catch(error => {
                                            showErrorAlert = true;
                                            errorMessage = error.message || '{{ __('consultation::consultation.Failed to delete consultation') }}';
                                            setTimeout(() => { showErrorAlert = false }, 5000);
                                        })"
                                        variant="ghost"
                                        size="xs"
                                        icon="trash"
                                        sr-text="{{ __('consultation::consultation.Remove') }}"
                                    />
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        
        <div 
            x-show="showNoConsultationMessage" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
        >
            <div class="flex items-center justify-center min-h-screen px-4">
                <div 
                    class="fixed inset-0 bg-black opacity-30"
                    @click="showNoConsultationMessage = false"
                ></div>
                
                <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl mx-auto max-w-md w-full p-6">
                    <div class="flex justify-between items-start mb-4">
                        <flux:heading
                            size="lg"
                            level="3"
                            class="dark:text-zinc-100"
                            x-text="noConsultationDate ? new Date(noConsultationDate).toLocaleDateString('pl-PL', {day: 'numeric', month: 'long', year: 'numeric'}) : ''"
                        />
                        <flux:button
                            @click="showNoConsultationMessage = false"
                            variant="ghost"
                            size="sm"
                            icon="x-mark"
                            sr-text="{{ __('consultation::consultation.Close') }}"
                        />
                    </div>
                    
                    <div class="text-center p-3 dark:bg-zinc-850 rounded-lg">
                        <flux:icon 
                            name="information-circle" 
                            class="h-12 w-12 text-zinc-400 dark:text-zinc-300 mx-auto mb-3" 
                        />
                        <flux:text class="dark:text-zinc-100">
                            {{ __('consultation::consultation.No consultations on this day') }}
                        </flux:text>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 