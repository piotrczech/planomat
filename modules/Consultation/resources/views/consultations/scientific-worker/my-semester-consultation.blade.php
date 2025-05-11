<x-layouts.app :title="__('consultation::consultation.My Semester Consultation')">
    <div class="max-w-7xl mx-auto pb-10">
        <x-consultation::consultations.consultation-header />

        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex-1">
                <x-common.card.base>
                    <livewire:consultation::consultations.scientific-worker.new-semester-consultation />
                </x-common.card.base>
            </div>

            <x-common.card.base class="flex-2">
                <livewire:consultation::consultations.scientific-worker.my-semester-consultation-calendar />
            </x-common.card.base>
        </div>
    </div>
</x-layouts.app>
