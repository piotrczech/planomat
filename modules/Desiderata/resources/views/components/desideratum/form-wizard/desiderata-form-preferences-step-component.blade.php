<div class="p-4 md:p-6"
    x-data='{
        allCourseOptions: {{ json_encode($allCourseOptions) }},
        proficientCourseIds: @json($proficientCourseIds),
        wantedCourseIds: @json($wantedCourseIds),
        unwantedCourseIds: @json($unwantedCourseIds),
        unwantedCount: {{ count($unwantedCourseIds) }},
        showUnwantedCourseBadge: {{ count($unwantedCourseIds) >= 2 ? 'true' : 'false' }}
    }'
    x-init="
        let proficientCoursesSelect, wantedCoursesSelect, unwantedCoursesSelect;

        const initialProficientIds = proficientCourseIds.map(String);
        const initialWantedIds = wantedCourseIds.map(String);
        const initialUnwantedIds = unwantedCourseIds.map(String);

        const initialWantedOptions = allCourseOptions.filter(option =>
            initialProficientIds.includes(String(option.value)) &&
            !initialUnwantedIds.includes(String(option.value))
        );

        const initialUnwantedOptions = allCourseOptions.filter(option =>
            initialProficientIds.includes(String(option.value)) &&
            !initialWantedIds.includes(String(option.value))
        );

        function refreshWantedUnwantedOptionsAndSelection() {
            if (!proficientCoursesSelect || !wantedCoursesSelect || !unwantedCoursesSelect) return;

            const currentProficientIds = proficientCoursesSelect.items.map(String);
            let alpineWantedIds = wantedCourseIds.map(String); 
            let alpineUnwantedIds = unwantedCourseIds.map(String);

            const wantedOptions = allCourseOptions.filter(option =>
                currentProficientIds.includes(String(option.value)) &&
                !alpineUnwantedIds.includes(String(option.value))
            );
            wantedCoursesSelect.clearOptions();
            wantedCoursesSelect.addOption(wantedOptions);
            
            const currentWantedInTomSelect = wantedCoursesSelect.items.map(String);
            const validWantedToKeep = currentWantedInTomSelect.filter(id => wantedOptions.some(opt => String(opt.value) === id));
            wantedCoursesSelect.setValue(validWantedToKeep, true);

            if (JSON.stringify(alpineWantedIds.sort()) !== JSON.stringify(validWantedToKeep.sort())) {
                $wire.wantedCourseIds = validWantedToKeep;
                wantedCourseIds = [...validWantedToKeep];
            }

            const unwantedOptions = allCourseOptions.filter(option =>
                currentProficientIds.includes(String(option.value)) &&
                !validWantedToKeep.includes(String(option.value)) 
            );
            unwantedCoursesSelect.clearOptions();
            unwantedCoursesSelect.addOption(unwantedOptions);

            const currentUnwantedInTomSelect = unwantedCoursesSelect.items.map(String);
            const validUnwantedToKeep = currentUnwantedInTomSelect.filter(id => unwantedOptions.some(opt => String(opt.value) === id));
            unwantedCoursesSelect.setValue(validUnwantedToKeep, true);
            
            if (JSON.stringify(alpineUnwantedIds.sort()) !== JSON.stringify(validUnwantedToKeep.sort())) {
                $wire.unwantedCourseIds = validUnwantedToKeep;
                unwantedCourseIds = [...validUnwantedToKeep];
            }

            unwantedCount = unwantedCourseIds.length;
            showUnwantedCourseBadge = unwantedCourseIds.length >= 2;
        }

        proficientCoursesSelect = new TomSelect($refs.canTeachCoursesSelect, {
            plugins: ['remove_button'],
            options: allCourseOptions,
            items: proficientCourseIds,
            onChange: function(values) {
                $wire.proficientCourseIds = values;
                refreshWantedUnwantedOptionsAndSelection();
            }
        });

        wantedCoursesSelect = new TomSelect($refs.wantToTeachSelect, {
            plugins: ['remove_button'],
            options: initialWantedOptions,
            items: wantedCourseIds,
            onChange: function(values) {
                $wire.wantedCourseIds = values;
                refreshWantedUnwantedOptionsAndSelection();
            }
        });

        unwantedCoursesSelect = new TomSelect($refs.dontWantToTeachSelect, {
            plugins: ['remove_button'],
            maxItems: 2,
            options: initialUnwantedOptions,
            items: unwantedCourseIds,
            onChange: function(values) {
                $wire.unwantedCourseIds = values;
                unwantedCount = values.length;
                showUnwantedCourseBadge = values.length >= 2;
                refreshWantedUnwantedOptionsAndSelection();
            }
        });

        if (proficientCoursesSelect && wantedCoursesSelect && unwantedCoursesSelect) {
            refreshWantedUnwantedOptionsAndSelection();
        } else {
            console.error('One or more TomSelect instances failed to initialize before final refresh call.');
        }
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

            <div wire:ignore>
                <select
                    x-ref="canTeachCoursesSelect"
                    id="can-teach-courses"
                    class="w-full"
                    aria-labelledby="course-preferences-legend"
                    multiple
                >
                    {{-- Opcje będą ładowane przez TomSelect --}}
                </select>
            </div>

            @error('proficientCourseIds') 
                <flux:text class="text-red-500 dark:text-red-400 text-sm mt-2">
                    {{ $message }}
                </flux:text>
            @enderror
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
                @error('masterThesesCount') 
                    <flux:text class="text-red-500 dark:text-red-400 text-sm">
                        {{ $message }}
                    </flux:text>
                @enderror

                <flux:input 
                    type="number" 
                    min="0"
                    id="bachelor-theses-count"
                    :label="__('desiderata::desiderata.Number of bachelor theses')" 
                    class="w-full {{ $errors->has('bachelorThesesCount') ? 'border-red-500 dark:border-red-400' : '' }}"
                    aria-labelledby="thesis-supervision-legend"
                    wire:model="bachelorThesesCount"
                />
                @error('bachelorThesesCount') 
                    <flux:text class="text-red-500 dark:text-red-400 text-sm">
                        {{ $message }}
                    </flux:text>
                @enderror
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
                @error('maxHoursPerDay') 
                    <flux:text class="text-red-500 dark:text-red-400 text-sm">
                        {{ $message }}
                    </flux:text>
                @enderror

                <flux:input 
                    type="number" 
                    min="1"
                    id="max-consecutive-hours"
                    :label="__('desiderata::desiderata.Maximum consecutive hours')" 
                    class="w-full {{ $errors->has('maxConsecutiveHours') ? 'border-red-500 dark:border-red-400' : '' }}"
                    aria-labelledby="working-hours-legend"
                    wire:model="maxConsecutiveHours"
                />
                @error('maxConsecutiveHours') 
                    <flux:text class="text-red-500 dark:text-red-400 text-sm">
                        {{ $message }}
                    </flux:text>
                @enderror
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