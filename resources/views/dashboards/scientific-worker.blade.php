<x-layouts.app :title="__('dashboard.Dashboard')">
    <flux:main>
        <div class="max-w-7xl mx-auto pb-10 flex w-full flex-1 flex-col gap-10">
            <div class="mt-8 mb-2">
                <flux:heading class="mb-1 !text-3xl font-bold">
                    {{ __('app.Planomat') }} 👋
                </flux:heading>
                <flux:text class="text-lg text-neutral-600 dark:text-neutral-400">
                    {{ __('dashboard.Welcome message') }}
                </flux:text>
            </div>
            
            <livewire:dashboard.scientific-worker.important-actions />
            
            <div>
                <flux:heading level="2" size="xl" class="mb-5">
                    {{ __('dashboard.Available Modules') }}
                </flux:heading>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <livewire:desiderata::dashboard.desiderata-card />
                    
                    <livewire:consultation::dashboard.consultations-card />
                </div>
            </div>
        </div> 
    </flux:main>
</x-layouts.app>