<x-layouts.app :title="__('dashboard.Dashboard')">
    <flux:main>
        {{-- Wrapper dla całej treści dashboardu, z większymi odstępami pionowymi --}}
        <div class="max-w-7xl mx-auto py-8 md:py-12 px-4 sm:px-6 lg:px-8 flex w-full flex-1 flex-col gap-y-10 md:gap-y-12">
            <flux:heading class="!text-2xl sm:!text-3xl font-bold text-neutral-800 dark:text-neutral-100">
                {{ __('app.Planomat Administration Panel') }} 🚀
            </flux:heading>

            <livewire:admin.dashboard.admin-dashboard-actions />

            {{-- Module Management Cards --}}
            <div class="bg-neutral-50 dark:bg-neutral-800/60 p-5 sm:p-6 rounded-xl">
                <flux:heading level="2" size="lg" class="mb-4 font-semibold text-neutral-700 dark:text-neutral-300">
                    {{ __('dashboard.Available Modules') }}
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

            <livewire:admin.dashboard.export-consultations-modal />
            <livewire:admin.dashboard.export-desiderata-modal />
        </div>
    </flux:main>
</x-layouts.app>