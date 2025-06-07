<x-layouts.app :title="__('admin_settings.Settings Panel')">
    <flux:main>
        <div class="max-w-7xl mx-auto py-8 md:py-12 px-4 sm:px-6 lg:px-8">
            <flux:heading class="!text-2xl sm:!text-3xl font-bold text-neutral-800 dark:text-neutral-100 mb-8">
                <flux:icon name="cog-8-tooth" class="w-8 h-8 mr-2 inline-block align-text-bottom" />
                {{ __('admin_settings.Application Settings') }}
            </flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Manage Courses --}}
                <a href="{{ route('admin.settings.general.courses') }}" wire:navigate
                   class="group block p-6 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl shadow-lg hover:shadow-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                    <div class="flex items-center justify-center mb-4">
                        <flux:icon name="academic-cap" class="w-12 h-12 text-primary-500 dark:text-primary-400 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
                    </div>
                    <flux:heading level="3" size="md" class="text-center font-semibold text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">
                        {{ __('admin_settings.Manage Courses') }}
                    </flux:heading>
                    <flux:text size="sm" class="text-center text-neutral-500 dark:text-neutral-400 mt-1">
                        {{ __('admin_settings.Manage Courses Description') }}
                    </flux:text>
                </a>

                {{-- Manage Semesters --}}
                <a href="{{ route('admin.settings.general.semesters') }}" wire:navigate
                   class="group block p-6 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl shadow-lg hover:shadow-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                    <div class="flex items-center justify-center mb-4">
                        <flux:icon name="calendar-days" class="w-12 h-12 text-primary-500 dark:text-primary-400 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
                    </div>
                    <flux:heading level="3" size="md" class="text-center font-semibold text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">
                        {{ __('admin_settings.Manage Semesters') }}
                    </flux:heading>
                    <flux:text size="sm" class="text-center text-neutral-500 dark:text-neutral-400 mt-1">
                        {{ __('admin_settings.Manage Semesters Description') }}
                    </flux:text>
                </a>

                {{-- Manage Users --}}
                <a href="{{ route('admin.settings.general.users') }}" wire:navigate
                   class="group block p-6 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl shadow-lg hover:shadow-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                    <div class="flex items-center justify-center mb-4">
                        <flux:icon name="users" class="w-12 h-12 text-primary-500 dark:text-primary-400 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
                    </div>
                    <flux:heading level="3" size="md" class="text-center font-semibold text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">
                        {{ __('admin_settings.users.Manage Users') }}
                    </flux:heading>
                    <flux:text size="sm" class="text-center text-neutral-500 dark:text-neutral-400 mt-1">
                        {{ __('admin_settings.users.Manage Users Description') }}
                    </flux:text>
                </a>
            </div>
        </div>
    </flux:main>
</x-layouts.app> 