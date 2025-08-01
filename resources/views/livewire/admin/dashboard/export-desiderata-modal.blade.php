<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="export-desiderata-modal-title" role="dialog" aria-modal="true" x-data="{ show: @entangle('showModal').live }" x-show="show" x-cloak>
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

        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-neutral-900 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
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
                    <h3 class="text-lg font-semibold leading-6 text-neutral-900 dark:text-neutral-100" id="export-desiderata-modal-title">
                        {{ __('admin_settings.desiderata.Export Desiderata') }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('admin_settings.desiderata.Choose desiderata report type') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <button
                        wire:click="export('all')"
                        type="button"
                        class="flex flex-col items-center justify-center p-6 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-sm hover:border-primary-500 hover:shadow-md transition-all duration-200"
                    >
                        <svg class="w-10 h-10 mb-3 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span class="font-semibold">{{ __('admin_settings.desiderata.Export All') }}</span>
                    </button>
                    <button
                        wire:click="export('unfilled')"
                        type="button"
                        class="flex flex-col items-center justify-center p-6 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-sm hover:border-amber-500 hover:shadow-md transition-all duration-200"
                    >
                        <svg class="w-10 h-10 mb-3 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-1.125 0-2.062.938-2.062 2.063v15.374c0 1.125.938 2.063 2.063 2.063h12.188c1.125 0 2.063-.938 2.063-2.063V8.625c0-1.125-.938-2.063-2.063-2.063h-4.5m-5.625 0a2.063 2.063 0 100 4.125 2.063 2.063 0 000-4.125zm-5.625 0h5.625" />
                        </svg>
                        <span class="font-semibold">{{ __('admin_settings.desiderata.Export Unfilled') }}</span>
                    </button>
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