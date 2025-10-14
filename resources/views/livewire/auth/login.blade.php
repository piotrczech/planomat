<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;
    public bool $showTraditionalLogin = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('app.auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('app.auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }

    public function mount(): void
    {
        if (Session::get('logged_via_usos_no_account', false)) {
            $this->redirectIntended(default: route('account.pending', absolute: false), navigate: true);
        }
    }

    public function toggleTraditionalLogin(): void
    {
        $this->showTraditionalLogin = !$this->showTraditionalLogin;
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('app.Log in to your account')" :description="__('app.Login methods description')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    @php($errorMsg = session('error') ?? request()->query('error'))

    @if($errorMsg)
        <flux:callout variant="danger" icon="exclamation-triangle" class="mb-4 text-center">
            <flux:callout.text>{{ urldecode($errorMsg) }}</flux:callout.text>
        </flux:callout>
    @endif

    <!-- Primary USOS Login Button -->
    <a
        href="{{ route('usos.login') }}"
       class="inline-flex items-center justify-center gap-2 w-full mt-4"
    >
        <flux:button variant="primary" class="w-full">
            <flux:icon name="key" class="h-5 w-5" />
            {{ __('app.Log in via USOS') }}
        </flux:button>
    </a>

    <!-- Traditional Login Toggle -->
    <div class="text-center">
        <button
            wire:click="toggleTraditionalLogin" 
            class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-800 dark:hover:text-neutral-200 underline"
        >
            {{ __('app.Log in with email and password') }}
        </button>
    </div>

    @if($showTraditionalLogin)
        <div class="border-t border-neutral-200 dark:border-neutral-700 pt-6">
            <form wire:submit="login" class="flex flex-col gap-6">
                <!-- Email Address -->
                <flux:input
                    wire:model="email"
                    :label="__('app.Email address')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@pwr.edu.pl"
                />

                <!-- Password -->
                <div class="relative">
                    <flux:input
                        wire:model="password"
                        :label="__('app.Password')"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('app.Password')"
                    />
                </div>

                <!-- Remember Me -->
                <flux:checkbox wire:model="remember" :label="__('app.Remember me')" />

                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('app.Log in') }}</flux:button>
                </div>
            </form>
        </div>
    @endif
</div>
