<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-700/75 dark:bg-neutral-800/75" aria-hidden="true"
             wire:click="closeModal" wire:keydown.escape.window="closeModal">
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-neutral-900 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
             role="document">
            <div class="sm:flex sm:items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full dark:bg-red-800/30 sm:mx-0 sm:h-10 sm:w-10">
                    <flux:icon name="exclamation-triangle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <flux:heading level="3" id="modal-title" class="text-lg font-medium leading-6 text-neutral-900 dark:text-neutral-100">
                        {{ __('admin_settings.semester_manager.Delete Semester Modal Title') }}
                    </flux:heading>
                    <div class="mt-2">
                        <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">
                            {{ __('admin_settings.semester_manager.Are you sure you want to delete the semester') }} <strong class="text-neutral-700 dark:text-neutral-200">{{ $semesterDetails }}</strong>? {{ __('admin_settings.semester_manager.This action cannot be undone.') }}
                        </flux:text>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                <flux:button wire:click="confirmDelete" variant="danger" class="w-full sm:ml-3 sm:w-auto">
                    {{ __('admin_settings.semester_manager.Delete') }} {{-- Użyj generycznego tłumaczenia jeśli pasuje --}}
                </flux:button>
                <flux:button wire:click="closeModal" class="w-full mt-3 sm:mt-0 sm:w-auto">
                    {{ __('admin_settings.semester_manager.Cancel') }}
                </flux:button>
            </div>
        </div>
    </div>
</div> 