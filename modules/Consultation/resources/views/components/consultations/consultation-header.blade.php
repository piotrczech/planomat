<div class="block md:flex md:items-start md:justify-between px-4 md:px-0">
    <div class="mr-6 mb-4 md:mb-0">
        <flux:heading class="mb-2 !text-3xl">
            {{ __('consultation::consultation.Manage consultation schedule') }}
        </flux:heading>

        <flux:text>
            <p class="hidden dark:block">
                {{ __('consultation::consultation.My Semester Consultation Description.Dark') }}
            </p>
            <p class="block dark:hidden">
                {{ __('consultation::consultation.My Semester Consultation Description.Light') }}
            </p>
        </flux:text>
    </div>
    <flux:text variant="subtle" class="text-left md:text-right min-w-56">
        @if(isset($lastUpdateDate) && $lastUpdateDate)
            <p>{{ __('consultation::consultation.Last updated by You') }}: {{ $lastUpdateDate }}</p>
        @else
            <p>{{ __('consultation::consultation.Last updated by You') }}: {{ __('consultation::consultation.Never') }}</p>
        @endif
    </flux:text>
</div>

<flux:button.group class="mx-4 md:mx-0 flex flex-col md:flex-row">
    <flux:button
        :variant="request()->routeIs('consultations.scientific-worker.my-semester-consultation')
            ? 'primary'
            : 'outline'"
        :href="route('consultations.scientific-worker.my-semester-consultation')"
        role="link"
        class="w-full md:w-auto"
    >
        {{ __('consultation::consultation.Semester Consultations') }}
    </flux:button>
    <flux:button
        :variant="request()->routeIs('consultations.scientific-worker.my-session-consultation')
            ? 'primary'
            : 'outline'"
        :href="route('consultations.scientific-worker.my-session-consultation')"
        role="link"
        class="w-full md:w-auto"
    >
        {{ __('consultation::consultation.Session Consultations') }}
    </flux:button>
    <flux:button
        :variant="request()->routeIs('consultations.scientific-worker.my-part-time-consultation')
            ? 'primary'
            : 'outline'"
        :href="route('consultations.scientific-worker.my-part-time-consultation')"
        role="link"
        class="w-full md:w-auto"
    >
        {{ __('consultation::consultation.Part-time Consultations') }}
    </flux:button>
</flux:button.group>

<flux:separator variant="subtle" class="my-8" />