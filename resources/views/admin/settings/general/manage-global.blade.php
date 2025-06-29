<x-layouts.app :title="__('admin_settings.Global Settings')">
    <flux:main>
        <div class="max-w-7xl mx-auto py-8 md:py-12 px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.settings.index') }}" wire:navigate
                   class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200">
                    <flux:icon name="arrow-left" class="w-5 h-5 mr-2" />
                    {{ __('admin_settings.Back to Settings') }}
                </a>
            </div>

            <flux:heading class="!text-2xl sm:!text-3xl font-bold text-neutral-800 dark:text-neutral-100 mb-2">
                <flux:icon name="globe-alt" class="w-8 h-8 mr-2 inline-block align-text-bottom" />
                {{ __('admin_settings.Global Settings') }}
            </flux:heading>
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400 mb-8">
                {{ __('admin_settings.Manage Global Settings Description') }}
            </flux:text>

            @livewire('admin.settings.global-settings')
        </div>
    </flux:main>
</x-layouts.app> 