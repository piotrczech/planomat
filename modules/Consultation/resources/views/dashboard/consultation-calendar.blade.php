<x-common.card.base :title="__('consultation::dashboard.Calendar')" class="flex flex-col h-full">
    <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach(\App\Enums\WeekdayEnum::cases() as $day)
                <flux:text class="text-center">
        <p>
                    {{ $day->shortLabel() }}
                </p>
            </flux:text>
        @endforeach
    </div>
    
    <div class="grid grid-cols-7 gap-1">
        @foreach($this->calendarDays as $day)
            <livewire:consultation::dashboard.calendar-day
                :day="$day['day']"
                :appointments="$day['appointments']"
            />
        @endforeach
    </div>
</x-card.base>