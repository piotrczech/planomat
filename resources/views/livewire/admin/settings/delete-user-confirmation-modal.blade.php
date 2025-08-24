<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="delete-user-modal-title" role="dialog" aria-modal="true" x-data="{ show: @entangle('isVisible').live }" x-show="show" x-cloak>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 transition-opacity bg-gray-700/75 dark:bg-neutral-800/75" aria-hidden="true"
             wire:click="cancel" {{-- Zakładamy, że DeleteUserConfirmationModal ma metodę cancel lub closeModal --}}
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        {{-- This span helps to center the modal contents vertically. --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-neutral-900 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
             role="document"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            {{-- Zawartość modala potwierdzenia usunięcia --}}
            <div class="sm:flex sm:items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full dark:bg-red-800/30 sm:mx-0 sm:h-10 sm:w-10">
                    {{-- Użycie bezpośrednio SVG ikony trójkąta ostrzegawczego, jeśli flux:icon nadal sprawia problemy --}}
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-medium leading-6 text-neutral-900 dark:text-neutral-100" id="delete-user-modal-title">
                        {{ __('admin_settings.users.modal_delete.Confirm Deletion Title') }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('admin_settings.users.modal_delete.Are you sure') }}
                            @if ($userToDelete)
                                <strong class="font-semibold text-neutral-700 dark:text-neutral-200">{{ $userToDelete->fullName() }} ({{ $userToDelete->email }})</strong>?
                            @else
                                {{ __('admin_settings.users.modal_delete.this user') }}?
                            @endif
                            {{ __('admin_settings.users.modal_delete.This action cannot be undone') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                <button wire:click="confirmDeletion" type="button" wire:loading.attr="disabled"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                    {{ __('admin_settings.users.modal_delete.Delete User') }}
                </button>
                <button wire:click="cancel" type="button" wire:loading.attr="disabled"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-neutral-700 bg-white border border-neutral-300 rounded-md shadow-sm hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:focus:ring-offset-neutral-900 sm:mt-0 sm:w-auto sm:text-sm disabled:opacity-50">
                    {{ __('admin_settings.users.modal_delete.Cancel') }}
                </button>
            </div>
        </div>
    </div>
</div> 