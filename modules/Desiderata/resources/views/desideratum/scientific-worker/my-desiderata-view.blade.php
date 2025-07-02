<x-layouts.app :title="__('desiderata::desiderata.My Desiderata')">
    <div class="max-w-7xl mx-auto pb-10">
        <div 
            x-data="{ showSuccessAlert: false }"
            x-init="
                window.addEventListener('desideratumSaved', () => {
                    showSuccessAlert = true;
                    window.scrollTo(0, 0);
                });
            "
            x-show="showSuccessAlert"
            x-collapse
            class="mb-6"
        >
            <div x-show="showSuccessAlert" x-transition>
                <flux:callout 
                    variant="success" 
                    icon="check-circle" 
                >
                    <flux:callout.heading>{{ __('desiderata::desiderata.Success') }}</flux:callout.heading>
                    <flux:callout.text>{{ __('desiderata::desiderata.Desiderata have been saved successfully') }}</flux:callout.text>

                    <x-slot name="controls">
                        <flux:button icon="x-mark" variant="ghost" x-on:click="showSuccessAlert = false" />
                    </x-slot>
                </flux:callout>
            </div>
        </div>

        @if(!($hasExistingDesiderata ?? false) && ($hasDefaultPrevDesiderata ?? false))
            <div
                x-data="{ showNoDesideratumAlert: true }"
                x-show="showNoDesideratumAlert"
                x-collapse
            >
                <div x-show="showNoDesideratumAlert" x-transition>
                    <flux:callout
                        variant="warning"
                        icon="exclamation-triangle"
                        class="mb-6"
                    >
                        <flux:callout.heading>{{ __('desiderata::desiderata.Attention') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('desiderata::desiderata.You do not have desiderata for the current semester yet. We have pre-filled the form with data from your last submission – please verify and save.') }}</flux:callout.text>

                        <x-slot name="controls">
                            <flux:button icon="x-mark" variant="ghost" x-on:click="showNoDesideratumAlert = false" />
                        </x-slot>
                    </flux:callout>
                </div>
            </div>
        @elseif($hasExistingDesiderata ?? false)
            <div
                x-data="{ showExistingDesideratumAlert: true }"
                x-show="showExistingDesideratumAlert"
                x-collapse
            >
                <div x-show="showExistingDesideratumAlert" x-transition>
                    <flux:callout 
                        variant="secondary" 
                        icon="information-circle" 
                        class="mb-6"
                    >
                        <flux:callout.heading>{{ __('desiderata::desiderata.Information') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('desiderata::desiderata.You already have desiderata submitted. You can still edit them.') }}</flux:callout.text>

                        <x-slot name="controls">
                            <flux:button icon="x-mark" variant="ghost" x-on:click="showExistingDesideratumAlert = false" />
                        </x-slot>
                    </flux:callout>
                </div>
            </div>
        @endif
        
        <div class="flex items-start justify-between mb-6">
            <div class="mr-6">
                <flux:heading class="mb-2 !text-3xl">
                    {{ __('desiderata::desiderata.Desiderata Form Semester', [
                        'semester' => strtolower($currentSemester->season->label()),
                        'academicYear' => $currentSemester->academic_year
                    ]) }}
                </flux:heading>

                <flux:text>
                    <p>
                        {{ __('desiderata::desiderata.My Desiderata Description') }}
                    </p>
                </flux:text>
            </div>
            <flux:text variant="subtle" class="text-right min-w-56">
                @if(isset($lastUpdateDate) && $lastUpdateDate)
                    <p>{{ __('desiderata::desiderata.Last updated by You') }}: {{ $lastUpdateDate }}</p>
                @else
                    <p>{{ __('desiderata::desiderata.Last updated by You') }}: {{ __('desiderata::desiderata.Never') }}</p>
                @endif
            </flux:text>
        </div>

        <x-common.card.base>
            <livewire:desiderata::desideratum.scientific-worker.desiderata-form-wizard />
        </x-common.card.base>
    </div>
</x-layouts.app>
