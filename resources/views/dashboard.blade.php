<x-layouts.app :title="__('dashboard.Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <livewire:desiderata::dashboard.desiderata-card />
            
            <livewire:consultation::dashboard.consultations-card />
            
            <livewire:dash.news
                :news="$news ?? []"
            />
        </div>
        
        <livewire:consultation::dashboard.consultation-calendar />
    </div>
</x-layouts.app>
