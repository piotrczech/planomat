<div class="flex items-start justify-between">
    <div class="mr-6">
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
    <flux:text variant="subtle" class="text-right min-w-56">
        @if(isset($lastUpdateDate) && $lastUpdateDate)
            <p>{{ __('consultation::consultation.Last updated by You') }}: {{ $lastUpdateDate }}</p>
        @else
            <p>{{ __('consultation::consultation.Last updated by You') }}: {{ __('consultation::consultation.Never') }}</p>
        @endif
    </flux:text>
</div>

<flux:button.group>
    <flux:button
        :variant="request()->routeIs('consultations.scientific-worker.my-semester-consultation')
            ? 'primary'
            : 'outline'"
        :href="route('consultations.scientific-worker.my-semester-consultation')"
        role="link"
    >
        {{ __('consultation::consultation.Semester Consultations') }}
    </flux:button>
    <flux:button
        :variant="request()->routeIs('consultations.scientific-worker.my-session-consultation')
            ? 'primary'
            : 'outline'"
        :href="route('consultations.scientific-worker.my-session-consultation')"
        role="link"
    >
        {{ __('consultation::consultation.Session Consultations') }}
    </flux:button>
</flux:button.group>

<flux:separator variant="subtle" class="my-8" />