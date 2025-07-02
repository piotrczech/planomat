<div class="bg-neutral-50 dark:bg-neutral-800/60 p-5 sm:p-6 rounded-xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <flux:heading level="2" size="lg" class="mb-4 sm:mb-0 font-semibold text-neutral-700 dark:text-neutral-300">
            {{ __('dashboard.Quick Actions') }}
        </flux:heading>

        <div
            class="w-full sm:w-auto sm:min-w-[280px]"
            wire:ignore
            x-data="{
                semesters: {{ $this->semestersForTomSelect }},
                initialSelectedId: '{{ $selectedSemesterId }}',
                init() {
                    const select = new TomSelect(this.$refs.semesterSelect, {
                        options: this.semesters,
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        placeholder: '{{ __('admin_settings.Select a semester') }}',
                        onChange: (value) => {
                            $wire.set('selectedSemesterId', value);
                        }
                    });
                    if (this.initialSelectedId) {
                        select.setValue(this.initialSelectedId, true);
                    }
                }
            }"
        >
            <select x-ref="semesterSelect" class="w-full"></select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <button
            wire:click="openDesiderataExportModal"
            type="button"
            class="group flex flex-col items-center justify-center p-5 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1 shadow-sm"
        >
            <flux:icon name="arrow-down-tray" class="w-10 h-10 text-primary-500 dark:text-primary-400 mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
            <span class="text-sm font-semibold text-center text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ __('dashboard.Download All Desiderata PDF') }}</span>
        </button>
        <button wire:click="openConsultationExportModal" type="button" class="group flex flex-col items-center justify-center p-5 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1 shadow-sm">
            <flux:icon name="arrow-down-tray" class="w-10 h-10 text-primary-500 dark:text-primary-400 mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
            <span class="text-sm font-semibold text-center text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ __('dashboard.Download Consultations') }}</span>
        </button>
        <a href="{{ route('admin.settings.index') }}" wire:navigate class="group flex flex-col items-center justify-center p-5 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1 shadow-sm">
            <flux:icon name="cog-6-tooth" class="w-10 h-10 text-primary-500 dark:text-primary-400 mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
            <span class="text-sm font-semibold text-center text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ __('dashboard.Go to Settings') }}</span>
        </a>
    </div>
</div> 