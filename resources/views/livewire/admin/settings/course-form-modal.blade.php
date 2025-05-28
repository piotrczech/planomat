<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay, moved inside the flex container and click away is handled here --}}
        <div class="fixed inset-0 transition-opacity bg-gray-700/75 dark:bg-neutral-800/75" aria-hidden="true"
             wire:click="closeModal" wire:keydown.escape.window="closeModal">
        </div>

        {{-- This span helps to center the modal contents vertically. --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-neutral-900 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
             role="document">
            <form wire:submit.prevent="saveCourse">
                <div class="sm:flex sm:items-start">
                    <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <flux:heading level="3" id="modal-title" class="text-lg font-medium leading-6 text-neutral-900 dark:text-neutral-100">
                            {{ $courseId ? __('admin_settings.course_manager.Edit Course') : __('admin_settings.course_manager.Add Course') }}
                        </flux:heading>
                        <div class="mt-4">
                            <flux:input
                                wire:model.defer="name"
                                label="{{ __('admin_settings.course_manager.Course Name') }}"
                                id="course_name"
                                name="name"
                                required
                            />
                            @error('name')
                            <flux:text size="sm" color="danger" class="mt-1">{{ $message }}</flux:text>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                    <flux:button type="submit" variant="primary" class="w-full sm:ml-3 sm:w-auto">
                        {{ __('admin_settings.course_manager.Save') }}
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" class="w-full mt-3 sm:mt-0 sm:w-auto">
                        {{ __('admin_settings.course_manager.Cancel') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div> 