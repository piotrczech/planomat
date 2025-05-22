<x-layouts.app :title="__('dashboard.Dashboard')">
    <flux:main>
        {{-- Wrapper dla całej treści dashboardu, z większymi odstępami pionowymi --}}
        <div class="max-w-7xl mx-auto py-8 md:py-12 px-4 sm:px-6 lg:px-8 flex w-full flex-1 flex-col gap-y-10 md:gap-y-12">
            <flux:heading class="!text-2xl sm:!text-3xl font-bold text-neutral-800 dark:text-neutral-100">
                {{ __('app.Planomat Administration Panel') }} 🚀
            </flux:heading>
            
            {{-- Quick Actions --}}
            <div class="bg-neutral-50 dark:bg-neutral-800/60 p-5 sm:p-6 rounded-xl shadow-md">
                <flux:heading level="2" size="lg" class="mb-4 font-semibold text-neutral-700 dark:text-neutral-300">
                    {{ __('dashboard.Quick Actions') }}
                </flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <a href="{{ route('desiderata.dean-office.export.all-desiderata.pdf') }}" class="group flex flex-col items-center justify-center p-5 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl shadow-lg hover:shadow-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                        <flux:icon name="arrow-down-tray" class="w-10 h-10 text-primary-500 dark:text-primary-400 mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
                        <span class="text-sm font-semibold text-center text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ __('dashboard.Download All Desiderata PDF') }}</span>
                    </a>
                    <a href="{{ route('consultations.dean-office.export.all-consultations.pdf') }}" class="group flex flex-col items-center justify-center p-5 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl shadow-lg hover:shadow-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                        <flux:icon name="arrow-down-tray" class="w-10 h-10 text-primary-500 dark:text-primary-400 mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-300 transition-colors" />
                        <span class="text-sm font-semibold text-center text-neutral-700 dark:text-neutral-200 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ __('dashboard.Download All Consultations PDF') }}</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" wire:navigate class="group flex flex-col items-center justify-center p-5 bg-white dark:bg-neutral-800/70 border border-neutral-200 dark:border-neutral-700/80 rounded-xl shadow-lg hover:shadow-xl hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
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
                <div class="bg-white dark:bg-neutral-800/70 shadow-lg sm:rounded-xl overflow-hidden">
                    <div class="p-3 sm:p-4 border-b border-neutral-200 dark:border-neutral-700">
                        <flux:text size="xs" class="text-neutral-500 dark:text-neutral-400">
                            {{ __('dashboard.Activities last 2 weeks scrollable') }}
                        </flux:text>
                    </div>
                    <ul class="divide-y divide-neutral-200 dark:divide-neutral-700 max-h-96 overflow-y-auto">
                        {{-- Przykładowy wpis aktywności 1 --}}
                        <li class="p-3 sm:p-4 hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                            <div class="flex items-center justify-between space-x-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100 truncate">
                                        Anna Kowalska <span class="text-neutral-600 dark:text-neutral-300 font-normal">zaktualizowała konsultacje.</span>
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0 space-x-2">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Dzisiaj, 10:32</span>
                                    <flux:badge color="blue" variant="outline" size="sm">Konsultacje</flux:badge>
                                </div>
                            </div>
                        </li>
                        {{-- Przykładowy wpis aktywności 2 --}}
                        <li class="p-3 sm:p-4 hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                            <div class="flex items-center justify-between space-x-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100 truncate">
                                        Piotr Nowak <span class="text-neutral-600 dark:text-neutral-300 font-normal">złożył dezyderat.</span>
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0 space-x-2">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Wczoraj, 15:17</span>
                                    <flux:badge color="green" variant="outline" size="sm">Dezyderaty</flux:badge>
                                </div>
                            </div>
                        </li>
                        {{-- Przykładowy wpis aktywności 3 --}}
                        <li class="p-3 sm:p-4 hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                            <div class="flex items-center justify-between space-x-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100 truncate">
                                        Jan Wiśniewski <span class="text-neutral-600 dark:text-neutral-300 font-normal">zarejestrował się.</span>
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0 space-x-2">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">2 dni temu, 09:00</span>
                                    <flux:badge color="gray" variant="outline" size="sm">System</flux:badge>
                                </div>
                            </div>
                        </li>
                        {{-- Dodatkowy przykładowy wpis dla testu scrollowania (jeśli lista będzie długa) --}}
                        <li class="p-3 sm:p-4 hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                            <div class="flex items-center justify-between space-x-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100 truncate">
                                        Administrator <span class="text-neutral-600 dark:text-neutral-300 font-normal">zmienił ustawienia modułu.</span>
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0 space-x-2">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">3 dni temu, 11:05</span>
                                    <flux:badge color="purple" variant="outline" size="sm">Konfiguracja</flux:badge>
                                </div>
                            </div>
                        </li>
                        {{-- Dodatkowe przykładowe wpisy dla testu scrollowania --}}
                        <li class="p-3 sm:p-4 hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                            <div class="flex items-center justify-between space-x-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100 truncate">
                                        Magdalena Zając <span class="text-neutral-600 dark:text-neutral-300 font-normal">zaktualizowała konsultacje.</span>
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0 space-x-2">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">4 dni temu, 08:15</span>
                                    <flux:badge color="blue" variant="outline" size="sm">Konsultacje</flux:badge>
                                </div>
                            </div>
                        </li>
                        <li class="p-3 sm:p-4 hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                            <div class="flex items-center justify-between space-x-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100 truncate">
                                        Krzysztof Bąk <span class="text-neutral-600 dark:text-neutral-300 font-normal">złożył dezyderat.</span>
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0 space-x-2">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">5 dni temu, 12:00</span>
                                    <flux:badge color="green" variant="outline" size="sm">Dezyderaty</flux:badge>
                                </div>
                            </div>
                        </li>
                        <li class="p-3 sm:p-4 hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                            <div class="flex items-center justify-between space-x-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100 truncate">
                                        System <span class="text-neutral-600 dark:text-neutral-300 font-normal">wykonał automatyczną kopię zapasową.</span>
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0 space-x-2">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">6 dni temu, 03:00</span>
                                    <flux:badge color="gray" variant="outline" size="sm">System</flux:badge>
                                </div>
                            </div>
                        </li>
                         <li class="p-3 sm:p-4 hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                            <div class="flex items-center justify-between space-x-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100 truncate">
                                        Joanna Lewandowska <span class="text-neutral-600 dark:text-neutral-300 font-normal">zmieniła swoje hasło.</span>
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0 space-x-2">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">7 dni temu, 10:00</span>
                                    <flux:badge color="yellow" variant="outline" size="sm">Bezpieczeństwo</flux:badge>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts.app>