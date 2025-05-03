<div class="bg-white px-2 py-4 sm:py-5 dark:bg-neutral-800">
    @if(count($appointments) > 0)
        <div class="mt-2">
            @foreach($appointments as $appointment)
                <livewire:consultation::dashboard.calendar-appointment 
                    :time="$appointment['time']" 
                    :location="$appointment['location']" 
                    :weekType="$appointment['week_type']"
                    :wire:key="$loop->index" 
                />
            @endforeach
        </div>
    @endif
</div> 