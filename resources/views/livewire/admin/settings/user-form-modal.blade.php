<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="user-modal-title" role="dialog" aria-modal="true" 
     x-data="{ show: @entangle('isVisible').live }" 
     x-init="$watch('show', value => { /* console.log('UserFormModal Alpine x-init $watch: 'show' (entangled with isVisible) changed to [', value, '], current $wire.userId is [{{ $this->userId ?? 'null' }}]') */ })"
     x-show="show" 
     x-cloak>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 transition-opacity bg-gray-700/75 dark:bg-neutral-800/75" aria-hidden="true"
             @click="$wire.closeModal()"
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
            <form wire:submit.prevent="saveUser" id="user-form">
                <div class="flex items-center justify-between pb-3 border-b border-neutral-200 dark:border-neutral-700">
                    <flux:heading level="3" id="user-modal-title" class="text-lg font-medium leading-6 text-neutral-900 dark:text-neutral-100">
                        {{ $isEditing ? __('admin_settings.users.modal.Edit User') : __('admin_settings.users.modal.Add User') }}
                    </flux:heading>
                    <button type="button" @click="$wire.closeModal()" class="text-neutral-400 hover:text-neutral-500 dark:text-neutral-500 dark:hover:text-neutral-300">
                        <span class="sr-only">Close</span>
                        <flux:icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>

                <div class="mt-4 space-y-6">
                    {{-- Name --}}
                    <div>
                        <flux:label for="name">{{ __('admin_settings.users.form.Name') }}</flux:label>
                        <flux:input wire:model.defer="name" id="name" type="text" class="mt-1 block w-full" />
                        @error('name') <flux:text class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ $message }}</flux:text> @enderror
                        @error('data.name') <flux:text class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ $message }}</flux:text> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <flux:label for="email">{{ __('admin_settings.users.form.Email') }}</flux:label>
                        <flux:input wire:model.defer="email" id="email" type="email" class="mt-1 block w-full" />
                        @error('email') <flux:text class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ $message }}</flux:text> @enderror
                        @error('data.email') <flux:text class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ $message }}</flux:text> @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <flux:label for="password">{{ __('admin_settings.users.form.Password') }}</flux:label>
                        <flux:input wire:model.defer="password" id="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        @if($isEditing)
                            <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">{{ __('admin_settings.users.form.Password hint edit') }}</p>
                        @endif
                        @error('password') <flux:text class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ $message }}</flux:text> @enderror
                        @error('data.password') <flux:text class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ $message }}</flux:text> @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <flux:label for="password_confirmation">{{ __('admin_settings.users.form.Password Confirmation') }}</flux:label>
                        <flux:input wire:model.defer="password_confirmation" id="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        @error('password_confirmation') <flux:text class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ $message }}</flux:text> @enderror
                        @error('data.password_confirmation') <flux:text class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ $message }}</flux:text> @enderror
                    </div>
                </div>

                <div class="mt-6 sm:flex sm:flex-row-reverse pt-4 border-t border-neutral-200 dark:border-neutral-700">
                    <flux:button type="submit" form="user-form" variant="primary" class="w-full sm:ml-3 sm:w-auto" wire:loading.attr="disabled">
                        {{ $isEditing ? __('admin_settings.users.modal.Save Changes') : __('admin_settings.users.modal.Create User') }}
                    </flux:button>
                    <flux:button type="button" @click="$wire.closeModal()" class="w-full mt-3 sm:mt-0 sm:w-auto">
                        {{ __('admin_settings.users.modal.Cancel') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div> 