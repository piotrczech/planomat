<div
    x-data="{ 
        showSuccessAlert: false,
        showErrorAlert: false,
        showConsultationDetails: false,
        showNoConsultationMessage: false,
        selectedConsultation: null,
        showConfirmDeleteModal: false,
        consultationToDeleteId: null,
        workerSelect: null,
        truncateText(text, maxLength) {
            if (!text) return '';
            if (text == null || typeof text === 'undefined') return '';
            if (text.length <= maxLength) {
                return text;
            }
            return text.substring(0, maxLength) + '...';
        }
    }"
    x-init="
        initTomSelect();
        
        $watch('$wire.showComparison', (value) => {
            if (value) {
                $nextTick(() => {
                    initTomSelect();
                });
            } else {
                if (workerSelect) {
                    workerSelect.destroy();
                    workerSelect = null;
                }
            }
        });
        
        $watch('$wire.successMessage', (value) => {
            if (value) {
                showSuccessAlert = true;
                setTimeout(() => { 
                    showSuccessAlert = false;
                    $wire.successMessage = null;
                }, 5000);
            }
        });
        
        $watch('$wire.errorMessage', (value) => {
            if (value) {
                showErrorAlert = true;
                setTimeout(() => { 
                    showErrorAlert = false;
                    $wire.errorMessage = null;
                }, 5000);
            }
        });
        
        $wire.on('consultationDeleted', () => {
            showSuccessAlert = true;
            setTimeout(() => { 
                showSuccessAlert = false;
            }, 5000);
        });
        
        $watch('$wire.selectedWorkerId', (value) => {
            if (!value && workerSelect) {
                workerSelect.destroy();
                workerSelect = null;
            }
        });
        
        function initTomSelect() {
            const selectElement = document.getElementById('worker-select');
            if (selectElement) {
                if (workerSelect) {
                    workerSelect.destroy();
                    workerSelect = null;
                }
                
                if (!selectElement.tomselect) {
                    workerSelect = new TomSelect('#worker-select', {
                        create: false,
                        searchField: ['name'],
                        valueField: 'id',
                        labelField: 'name',
                        options: {{ json_encode($scientificWorkers) }},
                        onChange(value) {
                            $wire.selectWorker(value);
                        }
                    });
                }
            }
        }
    "
>
    <div class="mb-2 md:flex md:justify-between items-center">
        <div class="mb-2 md:mb-0">
            <flux:heading
                size="xl"
                level="2"
                class="mb-0"
                id="form-heading"
            >
                {{ $title }}
            </flux:heading>
        </div>

        <div class="md:flex justify-end md:ml-2 md:mb-0">
            <flux:badge size="sm" color="indigo" class="text-center md:text-right w-full md:w-auto">
                <div class="w-full md:w-auto">
                    {{ __('consultation::consultation.Total consultation time in your schedule') }}:
                    {{ $consultationSummaryTime }}
                </div>
            </flux:badge>
        </div>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4 w-full md:w-auto">
            <flux:button
                wire:click="toggleComparison"
                variant="outline"
                icon="users"
                :class="$showComparison ? 'bg-indigo-100 text-indigo-700' : ''"
                class="w-full md:w-auto"
            >
                {{ $showComparison ? __('consultation::consultation.Hide comparison') : __('consultation::consultation.Compare with colleague') }}
            </flux:button>
            
            @if($showComparison)
                <flux:button
                    wire:click="clearComparison"
                    variant="ghost"
                    icon="x-mark"
                    size="sm"
                >
                    {{ __('consultation::consultation.Clear comparison') }}
                </flux:button>
            @endif
        </div>
    </div>

    <flux:text class="mb-6">
        <p>
            {{ __('consultation::consultation.My semester consultation description') }}
        </p>
    </flux:text>

    @if($showComparison)
        <div class="mb-6 pb-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
            <flux:field>
                <flux:label>{{ __('consultation::consultation.Select colleague to compare') }}</flux:label>
                <div wire:ignore>
                    <select 
                        id="worker-select" 
                        class="w-full"
                    >
                        <option value="">{{ __('consultation::consultation.Choose a colleague') }}</option>
                        @foreach($scientificWorkers as $worker)
                            <option value="{{ $worker['id'] }}">{{ $worker['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </flux:field>
            
            @if($selectedWorkerName)
                <div class="mt-4 flex items-center justify-center space-x-8 text-sm">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-indigo-100 border border-indigo-300 rounded mr-2"></div>
                        <span class="font-medium">{{ __('consultation::consultation.Your consultations') }}</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-100 border border-green-300 rounded mr-2"></div>
                        <span class="font-medium">{{ __('consultation::consultation.Colleague consultations', ['name' => $selectedWorkerName]) }}</span>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Komunikaty sukcesu i błędu -->
    <div x-show="showSuccessAlert" x-transition class="mb-6">
        <flux:callout 
            variant="success" 
            icon="check-circle" 
        >
            <flux:callout.heading>{{ __('consultation::consultation.Success') }}</flux:callout.heading>
            <flux:callout.text>{{ $successMessage }}</flux:callout.text>

            <x-slot name="controls">
                <flux:button icon="x-mark" variant="ghost" x-on:click="showSuccessAlert = false" />
            </x-slot>
        </flux:callout>
    </div>

    <div x-show="showErrorAlert" x-transition class="mb-6">
        <flux:callout 
            variant="danger" 
            icon="exclamation-circle" 
        >
            <flux:callout.heading>{{ __('consultation::consultation.Error') }}</flux:callout.heading>
            <flux:callout.text>{{ $errorMessage }}</flux:callout.text>

            <x-slot name="controls">
                <flux:button icon="x-mark" variant="ghost" x-on:click="showErrorAlert = false" />
            </x-slot>
        </flux:callout>
    </div>

    <!-- Widok mobilny - lista konsultacji -->
    <div class="block md:hidden mb-6">
        <div class="space-y-3 md:px-2">
            @php
                $allMobileConsultations = [];
                
                foreach ($consultationEvents as $weekday => $events) {
                    foreach ($events as $event) {
                        $event['owner'] = 'me';
                        $event['ownerName'] = 'Ty';
                        $allMobileConsultations[] = $event;
                    }
                }
                
                // Add selected colleague consultations (if comparison is enabled)
                if ($showComparison && $selectedWorkerId && !empty($otherWorkerConsultationEvents)) {
                    foreach ($otherWorkerConsultationEvents as $weekday => $events) {
                        foreach ($events as $event) {
                            $event['owner'] = 'other';
                            $event['ownerName'] = $selectedWorkerName;
                            $allMobileConsultations[] = $event;
                        }
                    }
                }
                
                usort($allMobileConsultations, function($a, $b) {
                    $weekdayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                    $aIndex = array_search($a['weekday'], $weekdayOrder);
                    $bIndex = array_search($b['weekday'], $weekdayOrder);
                    
                    if ($aIndex === $bIndex) {
                        return strcmp($a['startTime'], $b['startTime']);
                    }
                    return $aIndex - $bIndex;
                });
            @endphp

            @foreach ($allMobileConsultations as $event)
                @php
                    if ($event['owner'] === 'me') {
                        $bgClass = 'bg-indigo-50 dark:bg-indigo-900/40';
                        $borderClass = 'border-indigo-200 dark:border-indigo-700/50';
                        $hoverClass = 'hover:bg-indigo-100 dark:hover:bg-indigo-900/50';
                        $textClass = 'text-indigo-800 dark:text-indigo-200';
                        $iconClass = 'text-indigo-600 dark:text-indigo-400';
                    } else {
                        $bgClass = 'bg-green-50 dark:bg-green-900/40';
                        $borderClass = 'border-green-200 dark:border-green-700/50';
                        $hoverClass = 'hover:bg-green-100 dark:hover:bg-green-900/50';
                        $textClass = 'text-green-800 dark:text-green-200';
                        $iconClass = 'text-green-600 dark:text-green-400';
                    }
                @endphp

                <div class="{{ $bgClass }} {{ $borderClass }} rounded-lg p-3 shadow-sm {{ $hoverClass }} transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="w-full">
                            <!-- Informacja o właścicielu -->
                            <div class="flex items-center {{ $event['owner'] === 'other' ? 'mb-1' : 'mb-2' }}">
                                <div 
                                    class="w-3 h-3 rounded mr-2 {{ $event['owner'] === 'me' ? 'bg-indigo-100 border border-indigo-300' : 'bg-green-100 border border-green-300' }}"
                                ></div>
                                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ $event['ownerName'] }}</span>
                            </div>

                            <!-- Dzień tygodnia -->
                            <div class="font-semibold {{ $textClass }} mb-2">
                                {{ \App\Domain\Enums\WeekdayEnum::from($event['weekday'])->label() }}
                            </div>

                            <!-- Czas -->
                            <div class="flex items-center mb-2">
                                <flux:icon name="clock" class="h-4 w-4 text-zinc-500 dark:text-zinc-400 mr-1.5" />
                                <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $event['startTime'] }} - {{ $event['endTime'] }}</span>
                            </div>

                            <!-- Lokalizacja -->
                            @if (!empty($event['locationBuilding']))
                                <div class="flex items-center mb-2">
                                    <flux:icon name="map-pin" class="h-4 w-4 text-zinc-500 dark:text-zinc-400 mr-1.5" />
                                    <span class="text-sm text-zinc-600 dark:text-zinc-300">
                                        {{ $event['locationBuilding'] }}{{ !empty($event['locationRoom']) ? ', ' . $event['locationRoom'] : '' }}
                                    </span>
                                </div>
                            @endif

                            <!-- Typ tygodnia -->
                            @if (isset($event['weekType']) && $event['weekType'] !== 'all')
                                <div class="flex items-center text-xs {{ $iconClass }} mb-2">
                                    <flux:icon name="calendar-days" class="h-3.5 w-3.5 text-zinc-500 dark:text-zinc-400 mr-1.5" />
                                    <span>{{ $event['weekType'] === 'even' ? __('consultation::consultation.Even weeks') : __('consultation::consultation.Odd weeks') }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Przycisk usuwania tylko dla własnych konsultacji -->
                        @if($event['owner'] === 'me')
                            <flux:button
                                @click="consultationToDeleteId = {{ $event['id'] }}; showConfirmDeleteModal = true;"
                                variant="ghost"
                                size="xs" 
                                icon="trash"
                                class="text-zinc-600 hover:text-red-500 dark:text-zinc-400 dark:hover:text-red-400 ml-2 flex-shrink-0"
                                sr-text="{{ __('consultation::consultation.Remove') }}"
                            />
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="hidden md:block dark:bg-zinc-900 rounded-lg">
        <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="min-w-[750px]">
                <div class="grid" style="grid-template-columns: auto repeat(5, 1fr);">
                    
                    <div class="bg-zinc-100 dark:bg-zinc-800 p-2 text-center font-medium">Godz</div>
                    @foreach (\App\Domain\Enums\WeekdayEnum::values(includeWeekend: false) as $weekday)
                        <div class="bg-zinc-100 dark:bg-zinc-800 p-2 text-center font-medium text-zinc-700 dark:text-zinc-300">
                            {{ \App\Domain\Enums\WeekdayEnum::from($weekday)->shortLabel() }}
                        </div>
                    @endforeach
                    
                    <div class="col-span-6">
                        <div class="grid" style="grid-template-columns: auto repeat(5, 1fr); grid-template-rows: repeat(56, 16px);">
                            
                            @php
                                $timeLabels = [];
                                $rowIndex = 1;
                                
                                for ($hour = 7; $hour <= 20; $hour++) {
                                    $timeLabels[$rowIndex] = sprintf('%02d:00', $hour);
                                    $rowIndex += 4;
                                }
                                
                                $timeToRowIndex = function($time) {
                                    $hour = (int)substr($time, 0, 2);
                                    $minute = (int)substr($time, 3, 2);
                                    
                                    $totalMinutes = $hour * 60 + $minute;
                                    $baseTime = 7 * 60;
                                    $minutesDiff = $totalMinutes - $baseTime;
                                    
                                    return 1 + ($minutesDiff / 15);
                                };
                            @endphp
                            
                            @foreach ($timeLabels as $row => $time)
                                <div class="p-1 border-r border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 flex items-center justify-center text-sm text-zinc-600 dark:text-zinc-400 font-medium" style="grid-row: {{ $row }} / span 4; grid-column: 1;">
                                    {{ $time }}
                                </div>
                            @endforeach
                            
                            @for ($row = 1; $row <= 56; $row++)
                                @for ($col = 2; $col <= 6; $col++)
                                    <div class="border-r border-b border-zinc-100 dark:border-zinc-800" style="grid-row: {{ $row }}; grid-column: {{ $col }};"></div>
                                @endfor
                            @endfor
                            
                            <!-- Combined consultations (own + selected colleague) -->
                            @php
                                $allConsultations = [];
                                
                                // Add own consultations
                                foreach ($consultationEvents as $weekday => $events) {
                                    foreach ($events as $event) {
                                        $event['owner'] = 'me';
                                        $event['ownerName'] = 'Ty';
                                        $allConsultations[$weekday][] = $event;
                                    }
                                }
                                
                                // Add selected colleague consultations (if comparison is enabled)
                                if ($showComparison && $selectedWorkerId && !empty($otherWorkerConsultationEvents)) {
                                    foreach ($otherWorkerConsultationEvents as $weekday => $events) {
                                        foreach ($events as $event) {
                                            $event['owner'] = 'other';
                                            $event['ownerName'] = $selectedWorkerName;
                                            $allConsultations[$weekday][] = $event;
                                        }
                                    }
                                }
                            @endphp

                            @foreach ($allConsultations as $weekday => $events)
                                @continue(!in_array($weekday, \App\Domain\Enums\WeekdayEnum::values(includeWeekend: false)))
                                @php
                                    usort($events, fn($a, $b) => strcmp($a['startTime'], $b['startTime']));
                                    
                                    $groups = [];

                                    foreach ($events as $event) {
                                        $added = false;

                                        foreach ($groups as $groupIndex => $group) {
                                            foreach ($group as $existingEvent) {
                                                if (
                                                    $event['startTime'] < $existingEvent['endTime'] &&
                                                    $event['endTime'] > $existingEvent['startTime']
                                                ) {
                                                    $groups[$groupIndex][] = $event;
                                                    $added = true;
                                                    break 2;
                                                }
                                            }
                                        }

                                        if (! $added) {
                                            $groups[] = [$event];
                                        }
                                    }

                                    $weekdayIndex = array_search($weekday, \App\Domain\Enums\WeekdayEnum::values(includeWeekend: false));
                                    $column = ($weekdayIndex !== false ? $weekdayIndex : 0) + 2;
                                @endphp

                                @foreach ($groups as $group)
                                    @php
                                        $minStartTime = min(array_column($group, 'startTime'));
                                        $maxEndTime = max(array_column($group, 'endTime'));

                                        $containerStartRow = (int)$timeToRowIndex($minStartTime);
                                        $containerEndRow = (int)$timeToRowIndex($maxEndTime);
                                        $containerRowSpan = max(1, $containerEndRow - $containerStartRow);
                                        
                                        // Determine color based on owners
                                        $hasMe = collect($group)->contains('owner', 'me');
                                        $hasOther = collect($group)->contains('owner', 'other');
                                        
                                        if ($hasMe && $hasOther) {
                                            // Mixed - gradient or special color
                                            $bgClass = 'bg-gradient-to-r from-indigo-100 to-green-100 dark:from-indigo-900/60 dark:to-green-900/60';
                                            $borderClass = 'border-indigo-300 dark:border-indigo-700/60';
                                            $textClass = 'text-indigo-900 dark:text-white';
                                        } elseif ($hasMe) {
                                            $bgClass = 'bg-indigo-100 dark:bg-indigo-900/60';
                                            $borderClass = 'border-indigo-300 dark:border-indigo-700/60';
                                            $textClass = 'text-indigo-900 dark:text-white';
                                        } else {
                                            $bgClass = 'bg-green-100 dark:bg-green-900/60';
                                            $borderClass = 'border-green-300 dark:border-green-700/60';
                                            $textClass = 'text-green-900 dark:text-white';
                                        }
                                    @endphp

                                    <div
                                        class="{{ $bgClass }} border {{ $borderClass }} rounded-md m-1 p-1 flex justify-center items-center text-center overflow-hidden group hover:opacity-80 transition-colors cursor-pointer"
                                        style="grid-row: {{ $containerStartRow }} / span {{ $containerRowSpan }}; grid-column: {{ $column }};"
                                        data-consulatation-list="{{ json_encode($group) }}"
                                        @click="showConsultationDetails = true; selectedConsultation = JSON.parse($event.currentTarget.dataset.consulatationList);"
                                    >
                                        <div class="text-xs font-medium {{ $textClass }}">
                                            @if (count($group) > 1)
                                                {{ trans_choice('consultation::consultation.overlapping_consultations_cta', count($group), ['count' => count($group)]) }}
                                            @else
                                                {{ $group[0]['startTime'] }} - {{ $group[0]['endTime'] }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Consultation details popup -->
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
                    >
                        {{ __('consultation::consultation.Consultation details') }}
                    </flux:heading>
                    <flux:button
                        @click="showConsultationDetails = false"
                        variant="ghost"
                        size="sm"
                        icon="x-mark"
                        sr-text="{{ __('consultation::consultation.Close') }}"
                    />
                </div>
                
                <div class="space-y-3" x-show="selectedConsultation">
                    <template x-for="consultation in selectedConsultation" :key="consultation.id">
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3 dark:bg-zinc-850">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center mb-2">
                                        <div 
                                            class="w-3 h-3 rounded mr-2"
                                            :class="consultation.owner === 'me' ? 'bg-indigo-100 border border-indigo-300' : 'bg-green-100 border border-green-300'"
                                        ></div>
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300" x-text="consultation.ownerName"></span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <flux:icon 
                                            name="clock" 
                                            class="h-4 w-4 text-zinc-500 dark:text-zinc-400 mr-1.5" 
                                        />
                                        <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                            <span x-text="consultation.startTime"></span> - <span x-text="consultation.endTime"></span>
                                        </span>
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
                                    
                                    <div class="flex items-center mt-1">
                                        <flux:icon 
                                            name="calendar-days" 
                                            class="h-4 w-4 text-zinc-500 dark:text-zinc-400 mr-1.5" 
                                        />
                                        <span
                                            class="text-sm text-zinc-600 dark:text-zinc-300" 
                                            x-text="consultation.weekType === 'all' ?
                                                '{{ __('consultation::consultation.Every week') }}'
                                                : consultation.weekType === 'even'
                                                    ? '{{ __('consultation::consultation.Even weeks') }}'
                                                    : '{{ __('consultation::consultation.Odd weeks') }}'
                                            "
                                        >
                                        </span>
                                    </div>
                                </div>
                                
                                <flux:button
                                    x-show="consultation.owner === 'me'"
                                    @click="consultationToDeleteId = consultation.id; showConsultationDetails = false; showConfirmDeleteModal = true"
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

    <!-- Confirm Delete Modal -->
    <div 
        x-show="showConfirmDeleteModal" 
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
                @click="showConfirmDeleteModal = false"
            ></div>
            
            <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl mx-auto max-w-md w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <flux:heading
                        size="lg"
                        level="3"
                        class="dark:text-zinc-100"
                    >
                        {{ __('consultation::consultation.Confirm Deletion') }}
                    </flux:heading>
                    <flux:button
                        @click="showConfirmDeleteModal = false"
                        variant="ghost"
                        size="sm"
                        icon="x-mark"
                        sr-text="{{ __('consultation::consultation.Close') }}"
                    />
                </div>
                
                <p class="text-zinc-700 dark:text-zinc-300 mb-4">
                    {{ __('consultation::consultation.Are you sure you want to delete this consultation?') }}
                </p>
                
                <div class="flex justify-end space-x-3">
                    <flux:button
                        @click="showConfirmDeleteModal = false"
                        variant="outline"
                        size="sm"
                    >
                        {{ __('consultation::consultation.Cancel') }}
                    </flux:button>
                    <flux:button
                        @click="$wire.removeConsultation(consultationToDeleteId); showConfirmDeleteModal = false"
                        variant="danger"
                        size="sm"
                    >
                        {{ __('consultation::consultation.Yes, delete') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</div>