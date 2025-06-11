<div>
    <div class="mb-5">
        <flux:heading level="2" size="xl" class="flex items-center gap-2">
            {{ __('dashboard.Important Actions') }}
            <flux:badge color="{{ $actions->anyActionsAvailable ? 'amber' : 'green' }}" size="sm" class="font-normal">{{
                ($actions->showDesiderata ? 1 : 0) +
                ($actions->showSemesterConsultations ? 1 : 0) +
                ($actions->showSessionConsultations ? 1 : 0)
            }}</flux:badge>
        </flux:heading>

        @if ($actions->anyActionsAvailable)
            <flux:text>
                <p>
                    {{ __('dashboard.Important Actions Description') }}
                </p>
            </flux:text>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-2 mt-5">
                @if ($actions->showDesiderata)
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
                                <p class="mb-0">{{ trans_choice('dashboard.due_days_desiderata', $actions->desiderataDueDays) }}</p>
                            </flux:text>
                        </div>
                        <div class="ml-auto self-center">
                            <flux:icon name="chevron-right" class="text-neutral-400 transition-transform group-hover:translate-x-1 dark:text-neutral-500 dark:group-hover:text-neutral-400" />
                        </div>
                    </a>
                @endif

                @if ($actions->showSemesterConsultations)
                    <!-- Konsultacje Semestralne -->
                    <a href="{{ route('consultations.scientific-worker.my-semester-consultation') }}" wire:navigate class="group flex cursor-pointer items-start gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition hover:bg-neutral-50 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-700/60 dark:hover:shadow-md dark:hover:shadow-black/10 dark:hover:border-neutral-600">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-500">
                            <flux:icon name="calendar" />
                        </div>
                        <div class="flex flex-col">
                            <flux:heading size="lg" level="3" class="mb-0">
                                {{ __('dashboard.File Consultation Hours') }}
                            </flux:heading>
                            <flux:text>
                                <p class="mb-0">{{ trans_choice('dashboard.semester_active_for_days', $actions->semesterActiveForDays) }}</p>
                            </flux:text>
                        </div>
                        <div class="ml-auto self-center">
                            <flux:icon name="chevron-right" class="text-neutral-400 transition-transform group-hover:translate-x-1 dark:text-neutral-500 dark:group-hover:text-neutral-400" />
                        </div>
                    </a>
                @endif

                 @if ($actions->showSessionConsultations)
                    <!-- Konsultacje Sesyjne -->
                    <a href="{{ route('consultations.scientific-worker.my-session-consultation') }}" wire:navigate class="group flex cursor-pointer items-start gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition hover:bg-neutral-50 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-700/60 dark:hover:shadow-md dark:hover:shadow-black/10 dark:hover:border-neutral-600">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 dark:bg-cyan-900/30 dark:text-cyan-500">
                            <flux:icon name="calendar-days" />
                        </div>
                        <div class="flex flex-col">
                            <flux:heading size="lg" level="3" class="mb-0">
                                {{ __('dashboard.File Session Consultation Hours') }}
                            </flux:heading>
                            <flux:text>
                                <p class="mb-0">{{ trans_choice('dashboard.due_days_session_consultation', $actions->sessionConsultationsDueDays) }}</p>
                            </flux:text>
                        </div>
                        <div class="ml-auto self-center">
                            <flux:icon name="chevron-right" class="text-neutral-400 transition-transform group-hover:translate-x-1 dark:text-neutral-500 dark:group-hover:text-neutral-400" />
                        </div>
                    </a>
                @endif
            </div>
        @else
            <flux:text>
                <p>
                    {{ __('dashboard.no_important_actions') }}
                </p>
            </flux:text>
        @endif
    </div>
</div> 