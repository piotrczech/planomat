<div class="p-4 md:p-6"
    x-data='{
        allCourseOptions: {{ json_encode($allCourseOptions) }},
        proficientCourseIds: [],

        wantedCourseIds: @json($wantedCourseIds),
        unwantedCourseIds: @json($unwantedCourseIds),

        unwantedCount: {{ count($unwantedCourseIds) }},
        showUnwantedCourseBadge: {{ count($unwantedCourseIds) >= 2 ? 'true' : 'false' }}
    }'
    x-init="
        let wantedCoursesSelect, unwantedCoursesSelect, possibleWantedCourseOptions, possibleUnwantedCourseOptions;

        function refreshWantedUnwantedOptionsAndSelection(select) {
            possibleWantedCourseOptions = allCourseOptions.filter(option =>
                !unwantedCourseIds.includes(option.value)
            );
            wantedCourseIds = wantedCourseIds.filter(id =>
                possibleWantedCourseOptions.some(option => option.value == id)
            );
            $wire.wantedCourseIds = wantedCourseIds;
            
            possibleUnwantedCourseOptions = allCourseOptions.filter(option =>
                !wantedCourseIds.includes(option.value)
            );

            unwantedCourseIds = unwantedCourseIds.filter(id =>
                possibleUnwantedCourseOptions.some(option => option.value == id)
            );
            $wire.unwantedCourseIds = unwantedCourseIds;

            if (wantedCoursesSelect) {
                wantedCoursesSelect.clearOptions();
                wantedCoursesSelect.addOptions(possibleWantedCourseOptions);
                wantedCoursesSelect.setValue(wantedCourseIds, true);
                wantedCoursesSelect.close();
            }

            if (unwantedCoursesSelect) {
                unwantedCoursesSelect.clearOptions();
                unwantedCoursesSelect.addOptions(possibleUnwantedCourseOptions);
                unwantedCoursesSelect.setValue(unwantedCourseIds, true);
                unwantedCoursesSelect.close();
            }

            if (select == 1) {
                if (wantedCoursesSelect && possibleWantedCourseOptions.length > 0) {
                    wantedCoursesSelect.open();
                }
            } else {
                if (unwantedCoursesSelect && possibleUnwantedCourseOptions.length > 0) {
                    unwantedCoursesSelect.open();
                }
            }
            
            proficientCourseIds = allCourseOptions.filter(option =>
                !wantedCourseIds.includes(option.value) && !unwantedCourseIds.includes(option.value)
            ).map(option => option.value);

            unwantedCount = unwantedCourseIds.length;
            showUnwantedCourseBadge = unwantedCourseIds.length >= 2;
        }

        refreshWantedUnwantedOptionsAndSelection();

        wantedCoursesSelect = new TomSelect($refs.wantToTeachSelect, {
            plugins: ['remove_button'],
            options: possibleWantedCourseOptions,
            items: wantedCourseIds,
            onChange: function(values) {
                wantedCourseIds = values.map(value => parseInt(value));
                refreshWantedUnwantedOptionsAndSelection(1);
            }
        });

        unwantedCoursesSelect = new TomSelect($refs.dontWantToTeachSelect, {
            plugins: ['remove_button'],
            maxItems: 2,
            options: possibleUnwantedCourseOptions,
            items: unwantedCourseIds,
            onChange: function(values) {
                unwantedCourseIds = values.map(value => parseInt(value));
                refreshWantedUnwantedOptionsAndSelection(2);
            }
        });
    "
>
    <flux:heading
        size="xl"
        level="2"
        id="form-heading"
    >
        {{ __('desiderata::desiderata.Stage: Teaching preferences') }}
    </flux:heading>

    <flux:text class="mb-8">
        <p>
            {{ __('desiderata::desiderata.Teaching preferences description') }}
        </p>
    </flux:text>

    <!-- Ogólny komunikat o błędach walidacji -->
    @if ($errors->any())
        <div class="mb-6">
            <flux:callout
                variant="danger"
                icon="exclamation-triangle"
            >
                <flux:callout.heading>{{ __('desiderata::desiderata.Validation errors') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('desiderata::desiderata.Please correct the errors in the form') }}
                </flux:callout.text>
            </flux:callout>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg">
            <flux:legend class="mb-4 text-lg font-semibold" id="teaching-mode-legend">
                {{ __('desiderata::desiderata.Teaching mode') }}
            </flux:legend>
            
            <div class="space-y-4">
                <flux:switch
                    :label="__('desiderata::desiderata.Stationary studies')"
                    align="left"
                    aria-labelledby="teaching-mode-legend"
                    wire:model="wantStationary"
                />
                @error('wantStationary') 
                    <flux:text class="text-red-500 dark:text-red-400 text-sm">
                        {{ $message }}
                    </flux:text>
                @enderror
                
                <flux:switch
                    :label="__('desiderata::desiderata.Non-stationary studies')"
                    align="left"
                    aria-labelledby="teaching-mode-legend"
                    wire:model="wantNonStationary"
                />
                @error('wantNonStationary') 
                    <flux:text class="text-red-500 dark:text-red-400 text-sm">
                        {{ $message }}
                    </flux:text>
                @enderror
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg">
            <flux:legend class="mb-4 text-lg font-semibold" id="overtime-legend">
                {{ __('desiderata::desiderata.Overtime') }}
            </flux:legend>

            <flux:switch
                :label="__('desiderata::desiderata.I agree to work overtime')"
                align="left"
                aria-labelledby="overtime-legend"
                wire:model="agreeToOvertime"
            />
            @error('agreeToOvertime') 
                <flux:text class="text-red-500 dark:text-red-400 text-sm mt-2">
                    {{ $message }}
                </flux:text>
            @enderror
        </div>
    </div>

    <div class="bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg mb-8">
        <flux:legend class="mb-4 text-lg font-semibold" id="course-preferences-legend">
            {{ __('desiderata::desiderata.Course preferences') }}
        </flux:legend>

        <flux:description class="mb-6">
            {{ __('desiderata::desiderata.Course preferences description') }}
        </flux:description>
    
        <div class="mb-6">
            <flux:label for="can-teach-courses" class="block mb-2 font-medium">
                {{ __('desiderata::desiderata.Courses I can teach') }}
            </flux:label>

            <div
                class="flex gap-2 mt-2 transition-all duration-300 ease-in-out bg-white dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700/80 shadow-xs rounded-lg p-2 min-h-12"
                x-transition
            >
                @foreach ($allCourseOptions as $courseOption)
                    <div
                        class="py-1 px-2 bg-white dark:bg-neutral-800/50 rounded-lg text-sm"
                        x-show="proficientCourseIds.includes({{ $courseOption['value'] }})"
                    >
                        {{ $courseOption['text'] }}
                    </div>
                    <flux:separator vertical x-show="proficientCourseIds.includes({{ $courseOption['value'] }})" />
                @endforeach
            </div>

        </div>

        <flux:description class="mb-6">
            {{ __('desiderata::desiderata.Course preferences description after courses I can teach') }}
        </flux:description>
    
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <flux:label for="want-to-teach" class="block mb-2 font-medium">
                    {{ __('desiderata::desiderata.I want to teach') }}
                    <flux:icon name="face-smile" class="w-4 h-4 ml-1 inline-flex align-text-bottom" aria-hidden="true" />
                </flux:label>
                
                <div wire:ignore>
                    <select
                        x-ref="wantToTeachSelect"
                        id="want-to-teach"
                        class="w-full"
                        aria-labelledby="course-preferences-legend"
                        multiple
                    >
                        {{-- Opcje będą ładowane przez TomSelect --}}
                    </select>
                </div>
                
                @error('wantedCourseIds') 
                    <flux:text class="text-red-500 dark:text-red-400 text-sm mt-2">
                        {{ $message }}
                    </flux:text>
                @enderror
            </div>
            <div>
                <flux:label for="dont-want-to-teach" class="block mb-2 font-medium">
                    {{ __('desiderata::desiderata.I do not want to teach') }}&nbsp;<span aria-live="polite" class="text-sm">(<span x-text="unwantedCount"></span>/2)</span>
                    <flux:icon name="face-frown" class="w-4 h-4 ml-1 inline-flex align-text-bottom" aria-hidden="true" />
                </flux:label>

                <div wire:ignore>
                    <select
                        x-ref="dontWantToTeachSelect"
                        id="dont-want-to-teach"
                        class="w-full"
                        aria-labelledby="course-preferences-legend"
                        multiple
                    >
                        {{-- Opcje będą ładowane przez TomSelect --}}
                    </select>
                </div>

                @error('unwantedCourseIds') 
                    <flux:text class="text-red-500 dark:text-red-400 text-sm mt-2">
                        {{ $message }}
                    </flux:text>
                @enderror

                <div
                    x-show="showUnwantedCourseBadge"
                    x-collapse
                >
                    <flux:badge
                        class="mt-3"
                        color="rose"
                        aria-live="assertive"
                    >
                        {{ __('desiderata::desiderata.Maximum limit reached') }}
                    </flux:badge>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg">
            <flux:legend class="mb-4 text-lg font-semibold" id="thesis-supervision-legend">
                {{ __('desiderata::desiderata.Thesis supervision') }}
            </flux:legend>

            <div class="space-y-4">
                <flux:input 
                    type="number" 
                    min="0"
                    id="master-theses-count"
                    :label="__('desiderata::desiderata.Number of master theses')" 
                    class="w-full {{ $errors->has('masterThesesCount') ? 'border-red-500 dark:border-red-400' : '' }}"
                    aria-labelledby="thesis-supervision-legend"
                    wire:model="masterThesesCount"
                />

                <flux:input 
                    type="number" 
                    min="0"
                    id="bachelor-theses-count"
                    :label="__('desiderata::desiderata.Number of bachelor theses')" 
                    class="w-full {{ $errors->has('bachelorThesesCount') ? 'border-red-500 dark:border-red-400' : '' }}"
                    aria-labelledby="thesis-supervision-legend"
                    wire:model="bachelorThesesCount"
                />
            </div>
        </div>
        
        <div class="bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg">
            <flux:legend class="mb-4 text-lg font-semibold" id="working-hours-legend">
                {{ __('desiderata::desiderata.Working hours') }}
            </flux:legend>

            <div class="space-y-4">
                <flux:input 
                    type="number" 
                    min="1"
                    id="max-hours-day"
                    :label="__('desiderata::desiderata.Maximum hours per day')" 
                    class="w-full {{ $errors->has('maxHoursPerDay') ? 'border-red-500 dark:border-red-400' : '' }}"
                    aria-labelledby="working-hours-legend"
                    wire:model="maxHoursPerDay"
                />

                <flux:input 
                    type="number" 
                    min="1"
                    id="max-consecutive-hours"
                    :label="__('desiderata::desiderata.Maximum consecutive hours')" 
                    class="w-full {{ $errors->has('maxConsecutiveHours') ? 'border-red-500 dark:border-red-400' : '' }}"
                    aria-labelledby="working-hours-legend"
                    wire:model="maxConsecutiveHours"
                />
            </div>
        </div>
    </div>
        
    <div class="flex justify-end items-center">
        <flux:button 
            wire:click="nextStep" 
            variant="primary"
            class="px-6 py-3"
            wire:loading.attr="disabled"
            wire:target="nextStep"
        >
            <span wire:loading.remove wire:target="nextStep">
                {{ __('desiderata::desiderata.Next step') }}
                <flux:icon.arrow-right class="inline-flex h-4 w-4 ml-2" aria-hidden="true" />
            </span>
            <span wire:loading wire:target="nextStep">
                {{ __('desiderata::desiderata.Validating...') }}
                <flux:icon name="arrow-path" class="w-4 h-4 ml-2 animate-spin" aria-hidden="true" />
            </span>
        </flux:button>
    </div>
</div>