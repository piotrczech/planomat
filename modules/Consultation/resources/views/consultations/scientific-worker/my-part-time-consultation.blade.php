<x-layouts.app :title="__('consultation::consultation.My Part-time Consultation')">
    <div class="max-w-7xl mx-auto pb-10">
        <x-consultation::consultations.consultation-header :lastUpdateDate="$lastUpdateDate" />

        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex-1">
                <x-common.card.base>
                    <livewire:consultation::consultations.scientific-worker.new-part-time-consultation />
                </x-common.card.base>
            </div>

            <x-common.card.base class="flex-2">
                <livewire:consultation::consultations.scientific-worker.my-part-time-consultation-calendar />
            </x-common.card.base>
        </div>
    </div>
</x-layouts.app> 