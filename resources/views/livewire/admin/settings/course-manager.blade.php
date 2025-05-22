<div>
    {{-- Początek kontenera dla zarządzania kursami --}}
    <div class="bg-white dark:bg-neutral-800/70 shadow-md sm:rounded-xl">
        <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
            <flux:heading level="3" size="md">
                {{ __('admin_settings.course_manager.Course List') }}
            </flux:heading>
            <flux:text size="sm" class="mt-1 text-neutral-500 dark:text-neutral-400">
                {{ __('admin_settings.course_manager.Course List Description') }}
            </flux:text>
        </div>

        <div class="p-6">
            {{-- Tutaj znajdzie się tabela z listą kursów oraz przycisk do dodawania nowego kursu --}}
            {{-- Przykład przycisku Dodaj --}}
            <div class="flex justify-end mb-4">
                <flux:button variant="primary" icon="plus">
                    {{ __('admin_settings.course_manager.Add Course') }}
                </flux:button>
            </div>

            {{-- Placeholder dla tabeli --}}
            <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-10 text-center">
                <flux:icon name="table-cells" class="w-12 h-12 mx-auto text-neutral-400 dark:text-neutral-500 mb-4" />
                <flux:text class="text-neutral-500 dark:text-neutral-400">
                    {{ __('admin_settings.course_manager.Course table placeholder') }}
                </flux:text>
            </div>

            {{-- Modal do dodawania/edycji kursu (na razie ukryty lub jako placeholder) --}}
            {{-- <livewire:admin.settings.course-form-modal /> --}}
        </div>
    </div>
    {{-- Koniec kontenera dla zarządzania kursami --}}
</div> 