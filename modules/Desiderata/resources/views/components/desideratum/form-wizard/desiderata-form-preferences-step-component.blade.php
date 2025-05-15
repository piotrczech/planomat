<div class="p-4 md:p-6"
    x-data="{
        proficientCourseIds: @json($proficientCourseIds),
        wantedCourseIds: @json($wantedCourseIds),
        unwantedCourseIds: @json($unwantedCourseIds),
        showUnwantedCourseBadge: false
    }"
    x-init="
        const proficientCoursesSelect = new TomSelect('#can-teach-courses', {
            plugins: ['remove_button'],
            items: proficientCourseIds
        });
        
        const wantedCoursesSelect = new TomSelect('#want-to-teach', {
            plugins: ['remove_button'],
            items: wantedCourseIds
        });
        
        const unwantedCoursesSelect = new TomSelect('#dont-want-to-teach', {
            plugins: ['remove_button'],
            maxItems: 2,
            items: unwantedCourseIds
        });
        
        proficientCoursesSelect.on('change', function(values) {
            $wire.proficientCourseIds = values;
        });
        
        wantedCoursesSelect.on('change', function(values) {
            $wire.wantedCourseIds = values;
        });
        
        unwantedCoursesSelect.on('change', function(values) {
            $wire.unwantedCourseIds = values;
            showUnwantedCourseBadge = values.length >= 2;
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
                <flux:switch
                    :label="__('desiderata::desiderata.Non-stationary studies')"
                    align="left"
                    aria-labelledby="teaching-mode-legend"
                    wire:model="wantNonStationary"
                />
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
                    id="can-teach-courses" 
                    class="w-full"
                    aria-labelledby="course-preferences-legend"
                    multiple
                >
                    <option value="1">
                        {{ __('desiderata::desiderata.Course 1') }}
                    </option>
                    <option value="2">
                        {{ __('desiderata::desiderata.Course 2') }}
                    </option>
                </select>
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
                        id="want-to-teach" 
                        class="w-full"
                        aria-labelledby="course-preferences-legend"
                        multiple
                    >
                        <option value="1">
                            {{ __('desiderata::desiderata.Course 1') }}
                        </option>
                        <option value="2">
                            {{ __('desiderata::desiderata.Course 2') }}
                        </option>
                    </select>
                </div>
            </div>
            <div>
                <flux:label for="dont-want-to-teach" class="block mb-2 font-medium">
                    {{ __('desiderata::desiderata.I do not want to teach') }} 
                    <span aria-live="polite" class="text-sm">({{ count($unwantedCourseIds) }}/2)</span>
                    <flux:icon name="face-frown" class="w-4 h-4 ml-1 inline-flex align-text-bottom" aria-hidden="true" />
                </flux:label>

                <div wire:ignore>
                    <select 
                        id="dont-want-to-teach" 
                        class="w-full"
                        aria-labelledby="course-preferences-legend"
                        multiple
                    >
                        <option value="1">
                            {{ __('desiderata::desiderata.Course 1') }}
                        </option>
                        <option value="2">
                            {{ __('desiderata::desiderata.Course 2') }}
                        </option>
                    </select>
                </div>

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
                    class="w-full"
                    aria-labelledby="thesis-supervision-legend"
                    wire:model="masterThesesCount"
                />

                <flux:input 
                    type="number" 
                    min="0"
                    id="bachelor-theses-count"
                    :label="__('desiderata::desiderata.Number of bachelor theses')" 
                    class="w-full"
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
                    class="w-full"
                    aria-labelledby="working-hours-legend"
                    wire:model="maxHoursPerDay"
                />

                <flux:input 
                    type="number" 
                    min="1"
                    id="max-consecutive-hours"
                    :label="__('desiderata::desiderata.Maximum consecutive hours')" 
                    class="w-full"
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
        >
            {{ __('desiderata::desiderata.Next step') }}
            <flux:icon.arrow-right class="inline-flex h-4 w-4 ml-2" aria-hidden="true" />
        </flux:button>
    </div>
</div>