<x-layouts.app :title="__('dashboard.Dashboard')">
    <flux:main>
        {{-- Wrapper dla całej treści dashboardu, z większymi odstępami pionowymi --}}
        <div class="max-w-7xl mx-auto py-8 md:py-12 px-4 sm:px-6 lg:px-8 flex w-full flex-1 flex-col gap-y-10 md:gap-y-12">
            <flux:heading class="!text-2xl sm:!text-3xl font-bold text-neutral-800 dark:text-neutral-100">
                {{ __('app.Planomat Administration Panel') }} 🚀
            </flux:heading>
            
            {{-- Quick Actions --}}
            <div class="bg-neutral-50 dark:bg-neutral-800/60 p-5 sm:p-6 rounded-xl">
                <flux:heading level="2" size="lg" class="mb-4 font-semibold text-neutral-700 dark:text-neutral-300">
                    {{ __('dashboard.Quick Actions') }}
                </flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <a href="{{ route('desiderata.dean-office.export.all-desiderata.pdf') }}" class="group flex flex-col items-center justify-center p-5 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                        <flux:icon name="arrow-down-tray" class="w-10 h-10 text-primary-500 dark:text-primary-400 mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
                        <span class="text-sm font-semibold text-center text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ __('dashboard.Download All Desiderata PDF') }}</span>
                    </a>
                    <a href="{{ route('consultations.dean-office.export.all-consultations.pdf') }}" class="group flex flex-col items-center justify-center p-5 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                        <flux:icon name="arrow-down-tray" class="w-10 h-10 text-primary-500 dark:text-primary-400 mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
                        <span class="text-sm font-semibold text-center text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ __('dashboard.Download All Consultations PDF') }}</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" wire:navigate class="group flex flex-col items-center justify-center p-5 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                        <flux:icon name="cog-6-tooth" class="w-10 h-10 text-primary-500 dark:text-primary-400 mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
                        <span class="text-sm font-semibold text-center text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ __('dashboard.Go to Settings') }}</span>
                    </a>
                </div>
            </div>

            {{-- Module Management Cards --}}
            <div>
                <flux:heading level="2" size="lg" class="mb-4 font-semibold text-neutral-700 dark:text-neutral-300">
                    {{ __('dashboard.Modules') }}
                </flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <livewire:desiderata::dashboard.desiderata-card />
                    <livewire:consultation::dashboard.consultations-card />
                </div>
            </div>

            {{-- Recent User Activities --}}
            <div>
                <flux:heading level="2" size="lg" class="mb-4 font-semibold text-neutral-700 dark:text-neutral-300">
                    {{ __('dashboard.Recent User Activities') }}
                </flux:heading>
                <livewire:admin.recent-activities-component />
            </div>
        </div>
    </flux:main>
</x-layouts.app>