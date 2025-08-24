<div>
    {{-- Controls: Search, Add Button --}}
    <div class="mb-6 flex justify-between items-center">
        {{-- Search Input --}}
        <div class="w-full md:w-1/3">
            <flux:input
                wire:model.live.debounce.300ms="userSearch"
                placeholder="{{ __('admin_settings.users.Search users by name or email') }}"
                id="userSearch"
                icon="magnifying-glass"
            />
        </div>

        {{-- Add Button --}}
        <flux:button wire:click="openCreateUserModal" variant="primary" icon="plus">
            {{ __('admin_settings.users.Add User') }}
        </flux:button>
    </div>

    {{-- Users Table --}}
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
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @if (auth()->check() && (auth()->user()->hasRole(\App\Domain\Enums\RoleEnum::ADMINISTRATOR) || auth()->user()->hasRole(\App\Domain\Enums\RoleEnum::DEAN_OFFICE_WORKER)) && !$user->hasRole(\App\Domain\Enums\RoleEnum::ADMINISTRATOR) && $user->id !== auth()->id() && $filterRole !== \App\Domain\Enums\RoleEnum::DEAN_OFFICE_WORKER)
                                    <flux:button
                                        wire:click="impersonateUser({{ $user->id }})"
                                        size="sm"
                                        variant="subtle"
                                        icon="identification"
                                    >
                                        {{ __('admin_settings.users.Impersonate User') }}
                                    </flux:button>
                                @endif
                                <flux:button
                                    wire:click="openEditUserModal({{ $user->id }})"
                                    size="sm"
                                    variant="subtle"
                                    icon="pencil-square"
                                >
                                    {{ __('admin_settings.course_manager.Edit') }}
                                </flux:button>
                                @if ($user->id !== auth()->id()) {{-- Prevent self-deletion --}}
                                    <flux:button
                                        wire:click="openDeleteConfirmationModal({{ $user->id }})"
                                        variant="danger"
                                        size="sm"
                                        icon="trash"
                                    >
                                        {{ __('admin_settings.course_manager.Delete') }}
                                    </flux:button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
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

    {{-- Modals --}}
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
        :userId="$deletingUserId"
        :is-visible="$showDeleteConfirmationModal"
        key="delete-user-modal-{{ $deletingUserId }}"
    />

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('refreshPageForImpersonation', (event) => {
                window.location.reload();
            });
        });
    </script>
</div> 