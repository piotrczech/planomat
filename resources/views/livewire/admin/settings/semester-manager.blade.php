<div>
    <div class="mb-6 flex justify-between items-center">
        <div class="w-1/3">
            <flux:input
                wire:model.live.debounce.300ms="semesterSearch"
                placeholder="{{ __('admin_settings.semester_manager.Search semesters') }}"
                id="semesterSearch"
            />
        </div>
        <flux:button wire:click="openCreateSemesterModal" variant="primary" icon="plus">
            {{ __('admin_settings.semester_manager.Add Semester') }}
        </flux:button>
    </div>

    <div class="bg-white dark:bg-neutral-800/70 shadow-md sm:rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.semester_manager.Start Year') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.semester_manager.Season') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.semester_manager.Semester Start Date') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.semester_manager.Session Start Date') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.semester_manager.End Date') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.semester_manager.Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($semesters as $semester)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                {{ $semester->start_year }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $semester->season->label() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $semester->semester_start_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $semester->session_start_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $semester->end_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <flux:button wire:click="openEditSemesterModal({{ $semester->id }})" size="sm" icon="pencil">
                                    {{ __('admin_settings.semester_manager.Edit') }}
                                </flux:button>
                                <flux:button wire:click="openDeleteConfirmationModal({{ $semester->id }})" variant="danger" size="sm" icon="trash">
                                    {{ __('admin_settings.semester_manager.Delete') }}
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="calendar-days" class="w-12 h-12 mx-auto text-neutral-400 dark:text-neutral-500 mb-4" />
                                    <flux:text class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ __('admin_settings.semester_manager.No semesters found') }}
                                    </flux:text>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($semesters->hasPages())
            <div class="p-4 border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                {{ $semesters->links() }}
            </div>
        @endif
    </div>

    @if ($showSemesterFormModal)
        <livewire:admin.settings.semester-form-modal
            :semesterId="$editingSemesterId"
            key="semester-form-modal-{{ $editingSemesterId ?? 'new' }}"
        />
    @endif

    @if ($showDeleteConfirmationModal)
        <livewire:admin.settings.delete-semester-confirmation-modal
            :semesterId="$deletingSemesterId"
            key="delete-semester-modal-{{ $deletingSemesterId }}"
        />
    @endif
</div> 