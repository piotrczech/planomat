<x-layouts.app :title="__('dashboard.Dashboard')">
    <div class="max-w-7xl mx-auto pb-10 flex w-full flex-1 flex-col gap-10">
        <div class="mt-8 mb-2">
            <flux:heading class="mb-1 !text-3xl font-bold">
                {{ __('app.Planomat') }} 👋
            </flux:heading>
            <flux:text class="text-lg text-neutral-600 dark:text-neutral-400">
                {{ __('dashboard.Welcome message') }}
            </flux:text>
        </div>
        
        <div class="mb-5">
            <flux:heading level="2" size="xl" class="flex items-center gap-2">
                {{ __('dashboard.Important Actions') }} 
                <flux:badge color="amber" size="sm" class="font-normal">2</flux:badge>
            </flux:heading>

            <flux:text>
                <p>
                    {{ __('dashboard.Important Actions Description') }}
                </p>
            </flux:text>
            
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-2">
                <!-- Dezyderat -->
                <a href="{{ route('desiderata.scientific-worker.my-desiderata') }}" wire:navigate class="group flex cursor-pointer items-start gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition hover:bg-neutral-50 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-700/60 dark:hover:shadow-md dark:hover:shadow-black/10 dark:hover:border-neutral-600">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-500">
                        <flux:icon name="document-check" />
                    </div>
                    <div class="flex flex-col">
                        <flux:heading size="lg" level="3" class="mb-0">
                            {{ __('dashboard.Complete Desiderata Form') }}
                        </flux:heading> 
                        <flux:text>
                            <p class="mb-0">{{ __('dashboard.Due days', ['days' => 2]) }}</p>
                        </flux:text>
                    </div>
                    <div class="ml-auto self-center">
                        <flux:icon name="chevron-right" class="text-neutral-400 transition-transform group-hover:translate-x-1 dark:text-neutral-500 dark:group-hover:text-neutral-400" />
                    </div>
                </a>
                
                <!-- Konsultacje -->
                <a href="{{ route('consultations.scientific-worker.my-semester-consultation') }}" wire:navigate class="group flex cursor-pointer items-start gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition hover:bg-neutral-50 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-700/60 dark:hover:shadow-md dark:hover:shadow-black/10 dark:hover:border-neutral-600">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-500">
                        <flux:icon name="calendar" />
                    </div>
                    <div class="flex flex-col">
                        <flux:heading size="lg" level="3" class="mb-0">
                            {{ __('dashboard.File Consultation Hours') }}
                        </flux:heading>
                        <flux:text>
                            <p class="mb-0">{{ __('dashboard.Due days', ['days' => 11]) }}</p>
                        </flux:text>
                    </div>
                    <div class="ml-auto self-center">
                        <flux:icon name="chevron-right" class="text-neutral-400 transition-transform group-hover:translate-x-1 dark:text-neutral-500 dark:group-hover:text-neutral-400" />
                    </div>
                </a>
            </div>
        </div>
        
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
</x-layouts.app>
