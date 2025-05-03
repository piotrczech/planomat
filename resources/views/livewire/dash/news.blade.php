<?php

use function Livewire\Volt\{state, computed};

state(['news' => []]);

$recentNews = computed(function() {
    return collect($this->news)->filter(fn($item) => $item['isRecent'] ?? false)->all();
});

$olderNews = computed(function() {
    return collect($this->news)->filter(fn($item) => !($item['isRecent'] ?? false))->all();
});

$hasOlderNews = computed(function() {
    return count($this->olderNews) > 0;
});

?>

<x-common.card.base
    :title="__('dashboard.News')" 
    :subtitle="__('dashboard.Important information')"
    class="sm:col-span-2 lg:col-span-1 h-full sm:h-auto flex flex-col"
>
    <div class="flex-grow overflow-y-auto pr-1.5 mt-4 max-h-[400px] sm:max-h-[250px] md:max-h-[300px] lg:max-h-[250px]">
        <ul class="space-y-2.5 mt-4">
            @if(count($news) > 0)
                @foreach($this->recentNews as $item)
                    <livewire:news.item 
                        :title="$item['title']" 
                        :description="$item['description']" 
                        :isRecent="true" 
                        :wire:key="'recent-'.$loop->index" 
                    />
                @endforeach
                
                @if($this->hasOlderNews)
                    <livewire:news.divider />
                    
                    @foreach($this->olderNews as $item)
                        <livewire:news.item 
                            :title="$item['title']" 
                            :description="$item['description']" 
                            :isRecent="false" 
                            :wire:key="'older-'.$loop->index" 
                        />
                    @endforeach
                @endif
            @else
                <livewire:news.item 
                    :title="__('dashboard.Desiderata form active')" 
                    :description="__('dashboard.Completion deadline: 15.05.2023')" 
                    :isRecent="true" 
                />
                <livewire:news.item 
                    :title="__('dashboard.Summer semester start')" 
                    :description="__('dashboard.Date: 01.03.2023')" 
                    :isRecent="true" 
                />
                
                <livewire:news.divider />
                
                <livewire:news.item 
                    :title="__('dashboard.Holiday break')" 
                    :description="__('dashboard.From 29.03.2023 to 04.04.2023')" 
                    :isRecent="false" 
                />
                <livewire:news.item 
                    :title="__('dashboard.Summer exam session period')" 
                    :description="__('dashboard.From 12.06.2023 to 30.06.2023')" 
                    :isRecent="false" 
                />
            @endif
        </ul>
    </div>
</x-common.card.base> 