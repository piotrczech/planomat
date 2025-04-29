<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('app.Appearance')" :subheading=" __('app.Update the appearance settings for your account')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('app.Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('app.Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('app.System') }}</flux:radio>
        </flux:radio.group>
    </x-settings.layout>
</section>
