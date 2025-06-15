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
                <flux:text class="text-lg text-neutral-600 dark:text-neutral-400">
                    @if ($currentSemester)
                        {{ __('dashboard.Current semester') }} {{ $currentSemester->name }} {{ $currentSemester->start_year }}
                    @else
                        {{ __('dashboard.No semester found please contact administrator. Functionality is not available.') }}
                    @endif
                </flux:text>
            </div>
            
            <livewire:dashboard.scientific-worker.important-actions />
            
            <div>
                <flux:heading level="2" size="xl" class="mb-5">
                    {{ __('dashboard.Available Modules') }}
                </flux:heading>
                
                @if($currentSemester)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <livewire:desiderata::dashboard.desiderata-card />
                        
                        <livewire:consultation::dashboard.consultations-card />
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        {{-- Wyłączone karty modułów --}}
                        <div class="relative">
                            <div class="opacity-30 pointer-events-none">
                                <livewire:desiderata::dashboard.desiderata-card />
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-zinc-800/80 rounded-lg">
                                <div class="text-center">
                                    <flux:icon name="lock-closed" class="mx-auto h-8 w-8 text-red-500 mb-2" />
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('dashboard.module_disabled_no_semester') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="relative">
                            <div class="opacity-30 pointer-events-none">
                                <livewire:consultation::dashboard.consultations-card />
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-zinc-800/80 rounded-lg">
                                <div class="text-center">
                                    <flux:icon name="lock-closed" class="mx-auto h-8 w-8 text-red-500 mb-2" />
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('dashboard.module_disabled_no_semester') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div> 
    </flux:main>
</x-layouts.app>