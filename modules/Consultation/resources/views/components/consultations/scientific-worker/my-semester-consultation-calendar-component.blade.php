<div
    x-data="{ 
        showSuccessAlert: false,
        showErrorAlert: false,
        showConsultationDetails: false,
        showNoConsultationMessage: false,
        selectedConsultation: null,
        showConfirmDeleteModal: false,
        consultationToDeleteId: null,
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
    "
>
    <div class="flex justify-between items-center mb-2">
        <flux:heading
            size="xl"
            level="2"
            class="mb-0"
            id="form-heading"
        >
            {{ $title }}
        </flux:heading>

        <div class="flex justify-end mb-2 ml-2 md:mb-0">
            <flux:badge size="sm" color="indigo" class="text-right">
                {{ __('consultation::consultation.Total consultation time in your schedule') }}:
                {{ $consultationSummaryTime }}
            </flux:badge>
        </div>
    </div>

    <flux:text class="mb-6">
        <p>
            {{ __('consultation::consultation.My semester consultation description') }}
        </p>
    </flux:text>

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
        <div class="space-y-3">
            @foreach ($consultationEvents as $weekday => $events)
                @foreach ($events as $event)
                    <div class="bg-indigo-50 dark:bg-indigo-900/40 border border-indigo-200 dark:border-indigo-700/50 rounded-lg p-3 shadow-sm hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div
                                class="w-full"
                                data-consulatation-list="{{ json_encode($events) }}"
                                @click="showConsultationDetails = true; selectedConsultation = JSON.parse($event.currentTarget.dataset.consulatationList);"
                                style="cursor: pointer;"
                            >
                                <div class="font-semibold text-indigo-800 dark:text-indigo-200 mb-1 flex items-center justify-between">
                                    <span>{{ \App\Domain\Enums\WeekdayEnum::from($event['weekday'])->label() }}</span>
                                    @if (!empty($event['location']))
                                        <span class="text-sm font-normal text-zinc-600 dark:text-zinc-300 truncate ml-2 max-w-[60%]" title="{{ $event['location'] }}">
                                            {{ $event['location'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center mb-1">
                                    <flux:icon name="clock" class="h-4 w-4 text-zinc-500 dark:text-zinc-400 mr-1.5" />
                                    <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $event['startTime'] }} - {{ $event['endTime'] }}</span>
                                </div>

                                @if (isset($event['weekType']) && $event['weekType'] !== 'every')
                                    <div class="flex items-center text-xs text-indigo-600 dark:text-indigo-400 mb-1">
                                        <flux:icon name="calendar-days" class="h-3.5 w-3.5 text-zinc-500 dark:text-zinc-400 mr-1.5" />
                                        <span>{{ $event['weekType'] === 'even' ? __('consultation::consultation.Even weeks') : __('consultation::consultation.Odd weeks') }}</span>
                                    </div>
                                @endif

                                <div class="flex items-center text-xs text-zinc-500 dark:text-zinc-400">
                                    <flux:icon name="pencil-square" class="h-3.5 w-3.5 text-zinc-500 dark:text-zinc-400 mr-1.5" />
                                    <span>{{ __('consultation::consultation.Created') }}: 
                                        @if(isset($event['created_at']))
                                            {{ \Carbon\Carbon::parse($event['created_at'])->translatedFormat('d M Y, H:i') }}
                                        @else
                                            {{ __('consultation::consultation.N/A') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <flux:button
                                @click="consultationToDeleteId = {{ $event['id'] }}; showConfirmDeleteModal = true;"
                                variant="ghost"
                                size="xs" 
                                icon="trash"
                                class="text-zinc-600 hover:text-red-500 dark:text-zinc-400 dark:hover:text-red-400 ml-2 flex-shrink-0"
                                sr-text="{{ __('consultation::consultation.Remove') }}"
                            />
                        </div>
                    </div>
                @endforeach
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
                            
                            @foreach ($consultationEvents as $weekday => $events)
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
                                    @endphp

                                    <div
                                        class="bg-indigo-100 dark:bg-indigo-900/60 border border-indigo-300 dark:border-indigo-700/60 rounded-md m-1 p-1 flex justify-center items-center text-center overflow-hidden group hover:bg-indigo-200 dark:hover:bg-indigo-800/80 transition-colors cursor-pointer"
                                        style="grid-row: {{ $containerStartRow }} / span {{ $containerRowSpan }}; grid-column: {{ $column }};"
                                        data-consulatation-list="{{ json_encode($group) }}"
                                        @click="showConsultationDetails = true; selectedConsultation = JSON.parse($event.currentTarget.dataset.consulatationList);"
                                    >
                                        <div class="text-xs font-medium text-indigo-900 dark:text-white">
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