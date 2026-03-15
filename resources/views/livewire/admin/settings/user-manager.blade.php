<div>
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="w-full md:w-1/3">
            <flux:input
                wire:model.live.debounce.300ms="userSearch"
                placeholder="{{ __('admin_settings.users.Search users by name or email') }}"
                id="userSearch"
                icon="magnifying-glass"
            />
        </div>

        <div class="flex w-full flex-wrap items-center justify-end gap-2 md:w-auto">
            <div class="inline-flex rounded-lg border border-neutral-200 bg-white p-1 dark:border-neutral-700 dark:bg-neutral-900">
                <button
                    type="button"
                    wire:click="switchViewFilter('active')"
                    class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ !$isArchivedView ? 'bg-primary-600 text-white' : 'text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-800' }}"
                >
                    {{ __('admin_settings.users.tabs.Active') }}
                </button>
                <button
                    type="button"
                    wire:click="switchViewFilter('archived')"
                    class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ $isArchivedView ? 'bg-primary-600 text-white' : 'text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-800' }}"
                >
                    {{ __('admin_settings.users.tabs.Archived') }}
                </button>
            </div>

            @if (!$isArchivedView)
                <flux:button wire:click="openCreateUserModal" variant="primary" icon="plus">
                    {{ __('admin_settings.users.Add User') }}
                </flux:button>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800/70 shadow-lg sm:rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.users.table.Title') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.users.table.First Name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.users.table.Last Name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.users.table.Email') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.users.table.Status') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.users.table.Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-neutral-100">
                                {{ $user->academic_title ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-neutral-100">
                                {{ $user->first_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-neutral-100">
                                {{ $user->last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $user->display_email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                @if ($user->trashed())
                                    <span class="text-xs">
                                        {{ __('admin_settings.users.status.Archived At', ['date' => $user->archived_at_formatted ?? '-']) }}
                                    </span>
                                @elseif ($user->is_active)
                                    <flux:badge color="green" variant="outline" size="sm">
                                        {{ __('admin_settings.users.status.Active') }}
                                    </flux:badge>
                                @else
                                    <flux:badge color="red" variant="outline" size="sm">
                                        {{ __('admin_settings.users.status.Suspended') }}
                                    </flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @if ($isArchivedView)
                                    <flux:dropdown position="bottom" align="end">
                                        <flux:button size="sm" variant="subtle" icon="ellipsis-horizontal">
                                            {{ __('admin_settings.users.table.Actions') }}
                                        </flux:button>

                                        <flux:menu>
                                            @if ($user->is_restore_allowed)
                                                <flux:menu.item as="button" type="button" wire:click="restoreUser({{ $user->id }})" icon="arrow-uturn-left">
                                                    {{ __('admin_settings.users.Restore User') }}
                                                </flux:menu.item>
                                            @else
                                                <flux:menu.item disabled icon="clock">
                                                    {{ __('admin_settings.users.Restore Expired') }}
                                                </flux:menu.item>
                                            @endif
                                        </flux:menu>
                                    </flux:dropdown>
                                @else
                                    <flux:dropdown position="bottom" align="end">
                                        <flux:button size="sm" variant="subtle" icon="ellipsis-horizontal">
                                            {{ __('admin_settings.users.table.Actions') }}
                                        </flux:button>

                                        <flux:menu>
                                            @if (auth()->check() && (auth()->user()->hasRole(\App\Domain\Enums\RoleEnum::ADMINISTRATOR) || auth()->user()->hasRole(\App\Domain\Enums\RoleEnum::DEAN_OFFICE_WORKER)) && !$user->hasRole(\App\Domain\Enums\RoleEnum::ADMINISTRATOR) && $user->id !== auth()->id() && $filterRole !== \App\Domain\Enums\RoleEnum::DEAN_OFFICE_WORKER && $user->is_active)
                                                <flux:menu.item as="button" type="button" wire:click="impersonateUser({{ $user->id }})" icon="identification">
                                                    {{ __('admin_settings.users.Impersonate User') }}
                                                </flux:menu.item>
                                            @endif

                                            <flux:menu.item as="button" type="button" wire:click="openEditUserModal({{ $user->id }})" icon="pencil-square">
                                                {{ __('admin_settings.course_manager.Edit') }}
                                            </flux:menu.item>

                                            <flux:menu.item as="button" type="button" wire:click="toggleUserActive({{ $user->id }})" icon="{{ $user->is_active ? 'pause-circle' : 'play-circle' }}">
                                                {{ $user->is_active ? __('admin_settings.users.Suspend User') : __('admin_settings.users.Activate User') }}
                                            </flux:menu.item>

                                            @if ($user->id !== auth()->id())
                                                <flux:menu.separator />
                                                <flux:menu.item as="button" type="button" wire:click="openArchiveConfirmationModal({{ $user->id }})" icon="archive-box">
                                                    {{ __('admin_settings.users.Archive User') }}
                                                </flux:menu.item>
                                            @endif
                                        </flux:menu>
                                    </flux:dropdown>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="users" class="w-12 h-12 mx-auto text-neutral-400 dark:text-neutral-500 mb-4" />
                                    <flux:text class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ __('admin_settings.users.No users found') }}
                                    </flux:text>
                                    @if(empty($userSearch))
                                    <flux:text size="xs" class="text-neutral-400 dark:text-neutral-600 mt-1">
                                        {{ __('admin_settings.users.Try changing filter or search term') }}
                                    </flux:text>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination & Per Page Selector --}}
        @if ($users->hasPages())
            <div class="p-4 flex items-center justify-between border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                {{-- Per Page Selector --}}
                <div class="flex items-center">
                    <label for="perPage" class="text-sm font-medium text-neutral-600 dark:text-neutral-300 mr-2">{{ __('admin_settings.users.table.Per Page') }}:</label>
                    <select wire:model.live="perPage" id="perPage" class="w-20 block text-sm p-2 border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-md shadow-sm">
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pagination Buttons --}}
                <div class="flex items-center space-x-2">
                    <flux:button wire:click="previousPage" :disabled="$users->onFirstPage()" icon="arrow-left">
                        {{ __('pagination.previous') }}
                    </flux:button>

                    <flux:button wire:click="nextPage" :disabled="!$users->hasMorePages()" icon:trailing="arrow-right">
                        {{ __('pagination.next') }}
                    </flux:button>
                </div>
            </div>
        @endif
    </div>

    @if ($showCreateUserModal)
        <livewire:admin.settings.user-form-modal
            :user-id="0" 
            :is-visible="true"
            :is-editing="false"
            :user-role="$filterRole"
            key="user-form-modal-create-{{ Str::random(5) }}"
        />
    @elseif ($showEditUserModal && $editingUserId)
        <livewire:admin.settings.user-form-modal
            :user-id="$editingUserId"
            :is-visible="true"
            :is-editing="true"
            :user-role="$filterRole"
            key="user-form-modal-edit-{{ $editingUserId }}-{{ Str::random(5) }}"
        />
    @endif

    <livewire:admin.settings.delete-user-confirmation-modal
        :userId="$archivingUserId"
        :is-visible="$showArchiveConfirmationModal"
        key="archive-user-modal-{{ $archivingUserId }}"
    />

    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="restore-user-error-modal-title" role="dialog" aria-modal="true" x-data="{ show: @entangle('showRestoreErrorModal').live }" x-show="show" x-cloak>
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-700/75 dark:bg-neutral-800/75" aria-hidden="true"
                 wire:click="closeRestoreErrorModal"
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

                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full dark:bg-red-800/30 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-neutral-900 dark:text-neutral-100" id="restore-user-error-modal-title">
                            {{ __('admin_settings.users.notifications.user_restore_failed_title') }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $restoreErrorMessage }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="closeRestoreErrorModal" type="button" wire:loading.attr="disabled"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-neutral-700 bg-white border border-neutral-300 rounded-md shadow-sm hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:focus:ring-offset-neutral-900 sm:w-auto sm:text-sm disabled:opacity-50">
                        {{ __('admin_settings.Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('refreshPageForImpersonation', (event) => {
                window.location.reload();
            });
        });
    </script>
</div> 
