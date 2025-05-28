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
            {{ __('consultation::consultation.My semester consultation') }}
        </flux:heading>

        <div class="flex justify-end mb-2 md:mb-0">
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
            @foreach ($consultationEvents as $event)
                <div class="bg-indigo-50 dark:bg-indigo-900/40 border border-indigo-200 dark:border-indigo-700/50 rounded-lg p-3 shadow-sm hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="w-full" @click="selectedConsultation = {{ json_encode($event) }}; showConsultationDetails = true;" style="cursor: pointer;">
                            <div class="font-semibold text-indigo-800 dark:text-indigo-200 mb-1 flex items-center justify-between">
                                <span>{{ \App\Enums\WeekdayEnum::cases()[$event['weekday']]->label() }}</span>
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
        </div>
    </div>

    <div class="hidden md:block dark:bg-zinc-900 rounded-lg">
        <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="min-w-[750px]">
                <!-- Główna siatka CSS Grid z 8 kolumnami (godziny + 7 dni) -->
                <div class="grid" style="grid-template-columns: auto repeat(7, 1fr);">
                    
                    <!-- Nagłówki dni tygodnia -->
                    <div class="bg-zinc-100 dark:bg-zinc-800 p-2 text-center font-medium"></div>
                    @foreach (\App\Enums\WeekdayEnum::cases() as $index => $day)
                        <div class="bg-zinc-100 dark:bg-zinc-800 p-2 text-center font-medium {{ in_array($index, [5, 6]) ? 'text-red-600 dark:text-red-400 font-bold' : 'text-zinc-700 dark:text-zinc-300' }}">
                            {{ $day->shortLabel() }}
                        </div>
                    @endforeach
                    
                    <!-- Zawartość kalendarza z godzinami i slotami czasowymi -->
                    <div class="col-span-8">
                        <div class="grid" style="grid-template-columns: auto repeat(7, 1fr); grid-template-rows: repeat(56, 16px);">
                            
                            @php
                                // Tablica wszystkich godzin pełnych
                                $timeLabels = [];
                                // Zaczynamy od wiersza 1
                                $rowIndex = 1;
                                
                                // Godziny od 7:00 do 20:00
                                for ($hour = 7; $hour <= 20; $hour++) {
                                    $timeLabels[$rowIndex] = sprintf('%02d:00', $hour);
                                    $rowIndex += 4; // Każda godzina to 4 kwadranse
                                }
                                
                                // Funkcja mapująca czas na indeks wiersza w siatce
                                $timeToRowIndex = function($time) {
                                    $hour = (int)substr($time, 0, 2);
                                    $minute = (int)substr($time, 3, 2);
                                    
                                    // Konwertuj czas na minuty od północy
                                    $totalMinutes = $hour * 60 + $minute;
                                    
                                    // Czas bazowy to 7:00 = 420 minut
                                    $baseTime = 7 * 60;
                                    
                                    // Różnica w minutach
                                    $minutesDiff = $totalMinutes - $baseTime;
                                    
                                    // Każde 15 minut to 1 jednostka w siatce
                                    return 1 + ($minutesDiff / 15);
                                };
                            @endphp
                            
                            <!-- Hours labels and horizontal lines -->
                            @foreach ($timeLabels as $row => $time)
                                <div class="p-1 border-r border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 flex items-center justify-center text-sm text-zinc-600 dark:text-zinc-400 font-medium" style="grid-row: {{ $row }} / span 4; grid-column: 1;">
                                    {{ $time }}
                                </div>
                            @endforeach
                            
                            <!-- Empty grid cells and vertical lines -->
                            @for ($row = 1; $row <= 56; $row++)
                                @for ($col = 2; $col <= 8; $col++)
                                    <div class="border-r border-b border-zinc-100 dark:border-zinc-800" style="grid-row: {{ $row }}; grid-column: {{ $col }};"></div>
                                @endfor
                            @endfor
                            
                            <!-- Consultation blocks -->
                            @foreach ($consultationEvents as $event)
                                @php
                                    $startRow = (int)$timeToRowIndex($event['startTime']);
                                    $endRow = (int)$timeToRowIndex($event['endTime']);
                                    $rowSpan = $endRow - $startRow;
                                    
                                    // If the block is too small, set the minimum height
                                    if ($rowSpan < 1) $rowSpan = 1;
                                    
                                    // Column for the weekday (+2 because the first column is the hours, and the index starts at 0)
                                    $column = $event['weekday'] + 2;
                                @endphp
                                
                                <div
                                    class="bg-indigo-100 dark:bg-indigo-900/60 border border-indigo-300 dark:border-indigo-700/60 rounded-md m-1 p-1 flex flex-col justify-between overflow-hidden relative group hover:bg-indigo-200 dark:hover:bg-indigo-800/80 transition-colors cursor-pointer" 
                                    style="grid-row: {{ $startRow }} / span {{ $rowSpan }}; grid-column: {{ $column }};"
                                    @click="selectedConsultation = {{ json_encode($event) }}; showConsultationDetails = true;"
                                >
                                    
                                    <div class="text-sm font-medium text-indigo-900 dark:text-white whitespace-nowrap mb-0.5 flex items-center">
                                        {{ $event['startTime'] }} - {{ $event['endTime'] }}
                                    </div>
                                </div>
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
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3 dark:bg-zinc-850">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="flex items-center">
                                    <flux:icon 
                                        name="clock" 
                                        class="h-4 w-4 text-zinc-500 dark:text-zinc-400 mr-1.5" 
                                    />
                                    <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                        <span x-text="selectedConsultation?.startTime"></span> - <span x-text="selectedConsultation?.endTime"></span>
                                    </span>
                                </div>
                                
                                <div class="flex items-center mt-1" x-show="selectedConsultation?.location">
                                    <flux:icon 
                                        name="map-pin" 
                                        class="h-4 w-4 text-zinc-500 dark:text-zinc-400 mr-1.5" 
                                    />
                                    <span class="text-sm text-zinc-600 dark:text-zinc-300" x-text="selectedConsultation?.location"></span>
                                </div>
                                
                                <div class="flex items-center mt-1" x-show="selectedConsultation?.weekType && selectedConsultation?.weekType !== 'every'">
                                    <flux:icon 
                                        name="calendar-days" 
                                        class="h-4 w-4 text-zinc-500 dark:text-zinc-400 mr-1.5" 
                                    />
                                    <span class="text-sm text-zinc-600 dark:text-zinc-300" 
                                          x-text="selectedConsultation?.weekType === 'even' ? '{{ __('consultation::consultation.Even weeks') }}' : '{{ __('consultation::consultation.Odd weeks') }}'">
                                    </span>
                                </div>
                            </div>
                            
                            <flux:button
                                @click="consultationToDeleteId = selectedConsultation?.id; showConsultationDetails = false; showConfirmDeleteModal = true"
                                variant="ghost"
                                size="xs"
                                icon="trash"
                                sr-text="{{ __('consultation::consultation.Remove') }}"
                            />
                        </div>
                    </div>
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