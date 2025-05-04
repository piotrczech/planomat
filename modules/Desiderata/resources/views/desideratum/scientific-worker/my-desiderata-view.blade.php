<x-layouts.app :title="__('desiderata::desiderata.My Desiderata')">
    <div class="max-w-7xl mx-auto pb-10">
        <div class="flex items-start justify-between mb-6">
            <div class="mr-6">
                <flux:heading class="mb-2 !text-3xl">
                    {{ __('desiderata::desiderata.Desiderata Form') }}
                </flux:heading>

                <flux:text>
                    <p>
                        {{ __('desiderata::desiderata.My Desiderata Description') }}
                    </p>
                </flux:text>
            </div>
            <flux:text variant="subtle" class="text-right min-w-56">
                <p>{{ __('desiderata::desiderata.Last updated by You') }}: 2025-05-04</p>
            </flux:text>
        </div>

        <x-common.card.base>
            <livewire:desiderata::desideratum.scientific-worker.desiderata-form-wizard />
        </x-common.card.base>
    </div>
</x-layouts.app>
