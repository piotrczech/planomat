<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 transition-opacity bg-gray-700/75 dark:bg-neutral-800/75" aria-hidden="true"
             wire:click="closeModal" wire:keydown.escape.window="closeModal">
        </div>

        {{-- This span helps to center the modal contents vertically. --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-neutral-900 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
             role="document">
            <form wire:submit.prevent="saveSemester">
                <div class="sm:flex sm:items-start">
                    <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <flux:heading level="3" id="modal-title" class="text-lg font-medium leading-6 text-neutral-900 dark:text-neutral-100">
                            {{ $semesterId ? __('admin_settings.semester_manager.Edit Semester') : __('admin_settings.semester_manager.Add Semester') }}
                        </flux:heading>
                        <div class="mt-4 space-y-4">
                            <div>
                                <flux:input
                                    wire:model.defer="start_year"
                                    label="{{ __('admin_settings.semester_manager.Start Year') }}"
                                    id="start_year"
                                    type="number"
                                    placeholder="{{ now()->year }}"
                                    required
                                />
                                @error('start_year') <flux:text size="sm" color="red" class="mt-1">{{ $message }}</flux:text> @enderror
                            </div>

                            <div>
                                <flux:select
                                    wire:model.defer="season"
                                    label="{{ __('admin_settings.semester_manager.Season') }}"
                                    id="season"
                                    required>
                                    <option value="">{{ __('admin_settings.please_select') }}</option> {{-- Dodaj klucz tłumaczenia --}}
                                    @foreach ($seasons as $seasonEnum)
                                        <option value="{{ $seasonEnum->value }}">{{ $seasonEnum->label() }}</option>
                                    @endforeach
                                </flux:select>
                                @error('season') <flux:text size="sm" color="red" class="mt-1">{{ $message }}</flux:text> @enderror
                            </div>

                            <div>
                                <flux:label for="semester_start_date">{{ __('admin_settings.semester_manager.Semester Start Date') }}</flux:label>
                                <div wire:ignore class="mt-1">
                                    <flux:input
                                        x-data="{ picker: null }"
                                        x-ref="semester_start_date_picker"
                                        x-init="picker = flatpickr($refs.semester_start_date_picker, { dateFormat: 'Y-m-d', locale: 'pl', altInput: true, altFormat: 'd.m.Y', onChange: (selectedDates, dateStr) => { $wire.set('semester_start_date', dateStr); } }); $watch('$wire.semester_start_date', value => picker.setDate(value, false));"
                                        id="semester_start_date"
                                        type="text"
                                        wire:model.defer="semester_start_date"
                                        class="w-full"
                                        placeholder="YYYY-MM-DD"
                                        required
                                    />
                                </div>
                                @error('semester_start_date') <flux:text size="sm" color="red" class="mt-1">{{ $message }}</flux:text> @enderror
                            </div>

                            <div>
                                <flux:label for="session_start_date">{{ __('admin_settings.semester_manager.Session Start Date') }}</flux:label>
                                <div wire:ignore class="mt-1">
                                    <flux:input
                                        x-data="{ picker: null }"
                                        x-ref="session_start_date_picker"
                                        x-init="picker = flatpickr($refs.session_start_date_picker, { dateFormat: 'Y-m-d', locale: 'pl', altInput: true, altFormat: 'd.m.Y', onChange: (selectedDates, dateStr) => { $wire.set('session_start_date', dateStr); } }); $watch('$wire.session_start_date', value => picker.setDate(value, false));"
                                        id="session_start_date"
                                        type="text"
                                        wire:model.defer="session_start_date"
                                        class="w-full"
                                        placeholder="YYYY-MM-DD"
                                        required
                                    />
                                </div>
                                @error('session_start_date') <flux:text size="sm" color="red" class="mt-1">{{ $message }}</flux:text> @enderror
                            </div>

                            <div>
                                <flux:label for="end_date">{{ __('admin_settings.semester_manager.End Date') }}</flux:label>
                                <div wire:ignore class="mt-1">
                                    <flux:input
                                        x-data="{ picker: null }"
                                        x-ref="end_date_picker"
                                        x-init="picker = flatpickr($refs.end_date_picker, { dateFormat: 'Y-m-d', locale: 'pl', altInput: true, altFormat: 'd.m.Y', onChange: (selectedDates, dateStr) => { $wire.set('end_date', dateStr); } }); $watch('$wire.end_date', value => picker.setDate(value, false));"
                                        id="end_date"
                                        type="text"
                                        wire:model.defer="end_date"
                                        class="w-full"
                                        placeholder="YYYY-MM-DD"
                                        required
                                    />
                                </div>
                                @error('end_date') <flux:text size="sm" color="red" class="mt-1">{{ $message }}</flux:text> @enderror
                            </div>

                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                    <flux:button type="submit" variant="primary" class="w-full sm:ml-3 sm:w-auto">
                        {{ __('admin_settings.semester_manager.Save') }}
                    </flux:button>
                    <flux:button type="button" wire:click="closeModal" class="w-full mt-3 sm:mt-0 sm:w-auto">
                        {{ __('admin_settings.semester_manager.Cancel') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div> 