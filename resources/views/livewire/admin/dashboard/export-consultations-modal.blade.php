<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="export-consultations-modal-title" role="dialog" aria-modal="true" x-data="{ show: @entangle('showModal').live }" x-show="show" x-cloak>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-700/75 dark:bg-neutral-800/75" aria-hidden="true"
             wire:click="closeModal"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-neutral-900 sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6"
             role="document"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <div>
                <div class="mt-3 text-center sm:mt-0 sm:text-left">
                    <h3 class="text-lg font-semibold leading-6 text-neutral-900 dark:text-neutral-100" id="export-consultations-modal-title">
                        {{ __('admin_settings.consultations.Export Consultations') }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('admin_settings.consultations.Choose consultation type to export') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="grid sm:grid-cols-2 gap-6">
                        {{-- Semester Column --}}
                        <div class="space-y-3">
                            <h4 class="text-md font-semibold text-center text-neutral-700 dark:text-neutral-300 border-b border-neutral-200 dark:border-neutral-700 pb-2 mb-3">
                                {{ __('admin_settings.consultations.Semester Consultations') }}
                            </h4>
                            <button
                                wire:click="export('semester')"
                                type="button"
                                class="flex w-full items-center p-3 text-left bg-white dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-sm hover:border-primary-500 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-all duration-200"
                            >
                                <flux:icon name="document-text" class="w-5 h-5 mr-3 text-neutral-500 dark:text-neutral-400"/>
                                <span class="font-semibold text-sm">Raport PDF</span>
                            </button>
                            <button
                                wire:click="export('semester_excel')"
                                type="button"
                                class="flex w-full items-center p-3 text-left bg-white dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-sm hover:border-primary-500 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-all duration-200"
                            >
                                <flux:icon name="table-cells" class="w-5 h-5 mr-3 text-neutral-500 dark:text-neutral-400"/>
                                <span class="font-semibold text-sm">Raport Excel</span>
                            </button>
                            <button
                                wire:click="export('unfilled_semester')"
                                type="button"
                                class="flex w-full items-center p-3 text-left bg-white dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-sm hover:border-primary-500 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-all duration-200"
                            >
                                <flux:icon name="document-minus" class="w-5 h-5 mr-3 text-neutral-500 dark:text-neutral-400" />
                                <span class="font-semibold text-sm">{{ __('admin_settings.consultations.Unfilled Semester Consultations') }}</span>
                            </button>
                        </div>
                        {{-- Session Column --}}
                        <div class="space-y-3">
                            <h4 class="text-md font-semibold text-center text-neutral-700 dark:text-neutral-300 border-b border-neutral-200 dark:border-neutral-700 pb-2 mb-3">
                                {{ __('admin_settings.consultations.Session Consultations') }}
                            </h4>
                            <button
                                wire:click="export('session')"
                                type="button"
                                class="flex w-full items-center p-3 text-left bg-white dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-sm hover:border-primary-500 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-all duration-200"
                            >
                                <flux:icon name="document-text" class="w-5 h-5 mr-3 text-neutral-500 dark:text-neutral-400"/>
                                <span class="font-semibold text-sm">Raport PDF</span>
                            </button>
                            <button
                                wire:click="export('session_excel')"
                                type="button"
                                class="flex w-full items-center p-3 text-left bg-white dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-sm hover:border-primary-500 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-all duration-200"
                            >
                                <flux:icon name="table-cells" class="w-5 h-5 mr-3 text-neutral-500 dark:text-neutral-400"/>
                                <span class="font-semibold text-sm">Raport Excel</span>
                            </button>
                            <button
                                wire:click="export('unfilled_session')"
                                type="button"
                                class="flex w-full items-center p-3 text-left bg-white dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-sm hover:border-primary-500 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-all duration-200"
                            >
                                <flux:icon name="document-minus" class="w-5 h-5 mr-3 text-neutral-500 dark:text-neutral-400" />
                                <span class="font-semibold text-sm">{{ __('admin_settings.consultations.Unfilled Session Consultations') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-6">
                <button wire:click="closeModal" type="button"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-neutral-700 bg-white border border-neutral-300 rounded-md shadow-sm hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:focus:ring-offset-neutral-900 sm:text-sm">
                    {{ __('admin_settings.Cancel') }}
                </button>
            </div>
        </div>
    </div>
</div> 