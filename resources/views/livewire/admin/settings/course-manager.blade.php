<div>
    {{-- Początek kontenera dla zarządzania kursami --}}
    <div class="mb-6 flex justify-between items-center">
        <div class="w-1/3">
            <flux:input
                wire:model.live.debounce.300ms="courseSearch"
                placeholder="{{ __('admin_settings.course_manager.Search courses') }}"
                id="courseSearch"
            />
        </div>
        <flux:button wire:click="openCreateCourseModal" variant="primary" icon="plus">
                    {{ __('admin_settings.course_manager.Add Course') }}
                </flux:button>
            </div>

    <div class="bg-white dark:bg-neutral-800/70 shadow-md sm:rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.course_manager.Name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            {{ __('admin_settings.course_manager.Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($courses as $course)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                {{ $course->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <flux:button wire:click="openEditCourseModal({{ $course->id }})" size="sm" icon="pencil">
                                    {{ __('admin_settings.course_manager.Edit') }}
                                </flux:button>
                                <flux:button wire:click="openDeleteConfirmationModal({{ $course->id }})" variant="danger" size="sm" icon="trash">
                                    {{ __('admin_settings.course_manager.Delete') }}
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="academic-cap" class="w-12 h-12 mx-auto text-neutral-400 dark:text-neutral-500 mb-4" />
                                    <flux:text class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ __('admin_settings.course_manager.No courses found') }}
                </flux:text>
            </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($courses->hasPages())
            <div class="p-4 border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                {{ $courses->links() }}
            </div>
        @endif
    </div>

    @if ($showCourseFormModal)
        <livewire:admin.settings.course-form-modal
            :courseId="$editingCourseId"
            key="course-form-modal-{{ $editingCourseId ?? 'new' }}"
        />
    @endif

    @if ($showDeleteConfirmationModal)
        <livewire:admin.settings.delete-course-confirmation-modal
            :courseId="$deletingCourseId"
            key="delete-course-modal-{{ $deletingCourseId }}"
        />
    @endif
    {{-- Koniec kontenera dla zarządzania kursami --}}
</div> 