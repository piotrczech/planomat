<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        {{-- Impersonation Bar --}}
        @impersonating($guard = null) {{-- $guard = null sprawdzi domyślny guard 'web' --}}
            <div class="bg-orange-500 dark:bg-orange-600 text-white py-2 px-4 text-center text-sm shadow-md z-50 relative">
                <span>
                    {!! __('app.impersonating_message', ['name' => auth()->user()->name]) !!}
                </span>
                <a href="{{ route('impersonate.leave') }}" class="font-semibold underline hover:text-orange-100 dark:hover:text-orange-200 ml-2">
                    {{ __('app.leave_impersonation') }}
                </a>
            </div>
        @endImpersonating

        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('app.Dashboard') }}
                </flux:navbar.item>

                <flux:navbar.item 
                    icon="ticket" 
                    :href="$activeDesiderataSemester ? route('desiderata.scientific-worker.my-desiderata') : null"
                    :class="!$activeDesiderataSemester ? 'opacity-50 cursor-not-allowed flex' : ''"
                    :title="!$activeDesiderataSemester ? __('app.functionality_disabled_no_semester') : null"
                    :current="request()->routeIs('desiderata.scientific-worker.my-desiderata')"
                    wire:navigate
                >
                    {{ __('app.My Desiderata') }}
                    @if(!$activeDesiderataSemester)
                        <flux:icon name="lock-closed" variant="mini" class="ml-1 text-red-500 inline-flex" />
                    @endif
                </flux:navbar.item>

                <flux:navbar.item
                    icon="megaphone"
                    :href="$activeConsultationSemester ? route('consultations.scientific-worker.my-semester-consultation') : null"
                    :class="!$activeConsultationSemester ? 'opacity-50 cursor-not-allowed flex' : ''"
                    :title="!$activeConsultationSemester ? __('app.functionality_disabled_no_semester') : null"
                    :current="
                        request()->routeIs('consultations.scientific-worker.my-semester-consultation')
                        || request()->routeIs('consultations.scientific-worker.my-session-consultation')
                        || request()->routeIs('consultations.scientific-worker.my-consultation')
                    "
                    wire:navigate
                >
                    {{ __('app.My Consultation') }}
                    @if(!$activeConsultationSemester)
                        <flux:icon name="lock-closed" variant="mini" class="ml-1 text-red-500 inline-flex" />
                    @endif
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('app.Repository')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="folder-git-2"
                        href="https://github.com/piotrczech/planomat"
                        target="_blank"
                        :label="__('app.Repository')"
                    />
                </flux:tooltip>
            </flux:navbar>

            <!-- Desktop User Menu -->
            <flux:dropdown position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.appearance')" icon="cog" wire:navigate>{{ __('app.Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('app.Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('app.Platform')">
                    <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('app.Dashboard') }}
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="ticket"
                        :href="$activeDesiderataSemester ? route('desiderata.scientific-worker.my-desiderata') : null"
                        :class="!$activeDesiderataSemester ? 'opacity-50 cursor-not-allowed' : ''"
                        :current="request()->routeIs('desiderata.scientific-worker.my-desiderata')"
                    >
                        {{ __('app.My Desiderata') }}
                        @if(!$activeDesiderataSemester)
                            <flux:icon name="lock-closed" variant="mini" class="ml-1 text-red-500" />
                        @endif
                    </flux:navlist.item>

                    <flux:navlist.item
                        icon="megaphone"
                        :href="$activeConsultationSemester ? route('consultations.scientific-worker.my-semester-consultation') : null"
                        :class="!$activeConsultationSemester ? 'opacity-50 cursor-not-allowed' : ''"
                        :current="
                            request()->routeIs('consultations.scientific-worker.my-semester-consultation')
                            || request()->routeIs('consultations.scientific-worker.my-session-consultation')
                            || request()->routeIs('consultations.scientific-worker.my-consultation')
                        "
                        wire:navigate
                    >
                        {{ __('app.My Consultation') }}
                        @if(!$activeConsultationSemester)
                            <flux:icon name="lock-closed" variant="mini" class="ml-1 text-red-500" />
                        @endif
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/piotrczech/planomat" target="_blank">
                    Zobacz kod na GitHub
                </flux:navlist.item>
            </flux:navlist>
        </flux:sidebar>

        <div class="mt-4">
            {{ $slot }}
        </div>

        @fluxScripts
    </body>
</html>
