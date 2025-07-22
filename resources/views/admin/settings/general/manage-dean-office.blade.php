<x-layouts.app :title="__('admin_settings.users.Manage Dean Office Users')">
    <flux:main>
        <div class="max-w-7xl mx-auto py-8 md:py-12 px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb/Back link --}}
            <div class="mb-6">
                <a href="{{ route('admin.settings.index') }}" wire:navigate
                   class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-150 ease-in-out">
                    <flux:icon name="arrow-left" class="w-5 h-5 mr-2" />
                    {{ __('admin_settings.Back to Settings') }}
                </a>
            </div>

            {{-- Page Header --}}
            <div class="mb-8">
                <flux:heading class="!text-2xl sm:!text-3xl font-bold text-neutral-800 dark:text-neutral-100 mb-2">
                    <flux:icon name="users" class="w-8 h-8 mr-3 inline-block align-middle" />
                    {{ __('admin_settings.users.Manage Dean Office Users') }}
                </flux:heading>
                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">
                    {{ __('admin_settings.users.Manage Dean Office Users Page Description') }}
                </flux:text>
            </div>

            {{-- Livewire User Manager Component --}}
            <livewire:admin.settings.user-manager :filter-role="\App\Domain\Enums\RoleEnum::DEAN_OFFICE_WORKER" />

        </div>
    </flux:main>
</x-layouts.app> 