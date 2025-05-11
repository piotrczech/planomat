<div>
    <flux:heading
        size="xl"
        level="2"
        id="form-heading"
    >
        {{ __('consultation::consultation.My semester consultation') }}
    </flux:heading>

    <flux:text class="mb-6">
        <p>
            {{ __('consultation::consultation.My semester consultation description') }}
        </p>
    </flux:text>

    <div class="flex justify-end mb-2">
        <flux:badge size="sm" color="indigo">
            {{ __('consultation::consultation.Total consultation time in your schedule') }}:
            6h 15min
        </flux:badge>
    </div>

    <!-- Widok mobilny - lista konsultacji -->
    <div class="block md:hidden mb-6">
        <div class="space-y-4">
            @foreach ($consultationEvents as $event)
                <div class="bg-indigo-50 dark:bg-indigo-900/60 border border-indigo-200 dark:border-indigo-700 rounded-lg p-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold text-indigo-900 dark:text-white">
                                {{ \App\Enums\WeekdayEnum::cases()[$event['weekday']]->label() }}, {{ $event['startTime'] }} - {{ $event['endTime'] }}
                            </div>
                            @if (!empty($event['location']))
                                <div class="text-sm text-indigo-800 dark:text-zinc-300 mt-1">
                                    {{ $event['location'] }}
                                </div>
                            @endif
                            @if (isset($event['weekType']) && $event['weekType'] !== 'every')
                                <div class="text-sm font-medium text-indigo-700 dark:text-zinc-300 mt-1">
                                    {{ $event['weekType'] === 'even' ? __('consultation::consultation.Even weeks') : __('consultation::consultation.Odd weeks') }}
                                </div>
                            @endif
                        </div>
                        <button 
                            wire:click="removeConsultation({{ $event['id'] }})" 
                            class="text-indigo-700 hover:text-red-500 dark:text-zinc-400 dark:hover:text-red-400 flex-shrink-0 p-1"
                            title="{{ __('consultation::consultation.Remove') }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
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
                            
                            <!-- Etykiety godzin i linie poziome -->
                            @foreach ($timeLabels as $row => $time)
                                <div class="border-r border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 flex items-center justify-center text-sm text-zinc-600 dark:text-zinc-400 font-medium" style="grid-row: {{ $row }} / span 4; grid-column: 1;">
                                    {{ $time }}
                                </div>
                            @endforeach
                            
                            <!-- Komórki pustej siatki i linie pionowe -->
                            @for ($row = 1; $row <= 56; $row++)
                                @for ($col = 2; $col <= 8; $col++)
                                    <div class="border-r border-b border-zinc-100 dark:border-zinc-800" style="grid-row: {{ $row }}; grid-column: {{ $col }};"></div>
                                @endfor
                            @endfor
                            
                            <!-- Bloki konsultacji -->
                            @foreach ($consultationEvents as $event)
                                @php
                                    $startRow = (int)$timeToRowIndex($event['startTime']);
                                    $endRow = (int)$timeToRowIndex($event['endTime']);
                                    $rowSpan = $endRow - $startRow;
                                    
                                    // Jeśli blok jest zbyt mały, ustaw minimalną wysokość
                                    if ($rowSpan < 1) $rowSpan = 1;
                                    
                                    // Kolumna odpowiadająca dniu tygodnia (+2 bo pierwsza kolumna to godziny, a indeks zaczyna się od 0)
                                    $column = $event['weekday'] + 2;
                                @endphp
                                
                                <div class="bg-indigo-100 dark:bg-indigo-900/80 border border-indigo-300 dark:border-indigo-700 rounded-md m-1 p-1 flex flex-col justify-between overflow-hidden" 
                                     style="grid-row: {{ $startRow }} / span {{ $rowSpan }}; grid-column: {{ $column }};">
                                    
                                    <div class="flex justify-between items-start">
                                        <div class="text-sm font-medium text-indigo-900 dark:text-white whitespace-nowrap">
                                            {{ $event['startTime'] }} - {{ $event['endTime'] }}
                                        </div>
                                        <button 
                                            wire:click="removeConsultation({{ $event['id'] }})" 
                                            class="text-indigo-700 hover:text-red-500 dark:text-zinc-400 dark:hover:text-red-400 flex-shrink-0"
                                            title="{{ __('consultation::consultation.Remove') }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    @if (!empty($event['location']))
                                        <div class="text-sm text-indigo-800 dark:text-zinc-300 truncate">
                                            {{ $event['location'] }}
                                        </div>
                                    @endif
                                    
                                    @if (isset($event['weekType']) && $event['weekType'] !== 'every')
                                        <div class="text-sm font-medium text-indigo-700 dark:text-zinc-300">
                                            {{ $event['weekType'] === 'even' ? __('consultation::consultation.Even weeks') : __('consultation::consultation.Odd weeks') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>