<div>
    <form wire:submit.prevent="save">
        <div class="flex flex-col gap-y-4">
            <div x-data="{ shown: false, timeout: null }"
                 x-init="@this.on('saved', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000) })"
                 x-show="shown"
                 x-transition:leave.duration.500ms
                 style="display: none;"
                 class="text-sm font-medium text-green-600">
                {{ __('admin_settings.Saved.') }}
            </div>

            <div
                wire:ignore
                x-data="{
                    semesters: {{ json_encode($semesters->map(fn($s) => ['id' => $s->id, 'name' => sprintf('%s %d/%s', $s->season->label(), $s->start_year, Str::of($s->start_year + 1)->substr(-2))])) }},
                    initialConsultationId: '{{ $activeSemesterForConsultationsId }}',
                    initialDesiderataId: '{{ $activeSemesterForDesiderataId }}',
                    init() {
                        const consultationSelect = new TomSelect(this.$refs.selectForConsultations, {
                            options: this.semesters,
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            placeholder: '{{ __('admin_settings.Select a semester') }}',
                            onChange: (value) => {
                                $wire.set('activeSemesterForConsultationsId', value);
                            }
                        });
                        consultationSelect.setValue(this.initialConsultationId, true);

                        const desiderataSelect = new TomSelect(this.$refs.selectForDesiderata, {
                            options: this.semesters,
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            placeholder: '{{ __('admin_settings.Select a semester') }}',
                            onChange: (value) => {
                                $wire.set('activeSemesterForDesiderataId', value);
                            }
                        });
                        desiderataSelect.setValue(this.initialDesiderataId, true);
                    }
                }"
                class="grid grid-cols-1 md:grid-cols-2 gap-6"
            >
                <div>
                    <flux:label for="activeSemesterForConsultationsId">
                        {{ __('admin_settings.Active semester for consultations') }}
                    </flux:label>
                    <select
                        id="activeSemesterForConsultationsId"
                        x-ref="selectForConsultations"
                    ></select>
                    @error('activeSemesterForConsultationsId') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <flux:label for="activeSemesterForDesiderataId">
                        {{ __('admin_settings.Active semester for desiderata') }}
                    </flux:label>
                    <select
                        id="activeSemesterForDesiderataId"
                        x-ref="selectForDesiderata"
                    ></select>
                    @error('activeSemesterForDesiderataId') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <flux:separator variant="subtle" class="my-8" />
            
            <div class="mb-6">
                <flux:heading size="lg" level="2" class="mb-2">{{ __('admin_settings.Email Notifications') }}</flux:heading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Semester Consultations -->
                <div class="space-y-4">
                    <flux:heading size="md" level="3">{{ __('admin_settings.Semester consultations') }}</flux:heading>
                    
                    <div>
                        <flux:checkbox 
                            wire:model.live="notificationsSemesterConsultationsEnabled"
                            label="{{ __('admin_settings.Enable notifications for missing semester consultations') }}"
                        />
                    </div>
                    
                    <div x-show="$wire.notificationsSemesterConsultationsEnabled">
                        <flux:field>
                            <flux:label>{{ __('admin_settings.Send notification after X days from semester start') }}</flux:label>
                            <flux:input 
                                type="number" 
                                wire:model="notificationsSemesterConsultationsDaysAfter"
                                min="1" 
                                max="20"
                            />
                            @error('notificationsSemesterConsultationsWeeksAfter') 
                                <flux:error>{{ $message }}</flux:error> 
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Desiderata -->
                <div class="space-y-4">
                    <flux:heading size="md" level="3">{{ __('admin_settings.Desiderata') }}</flux:heading>
                    
                    <div>
                        <flux:checkbox 
                            wire:model.live="notificationsDesiderataEnabled"
                            label="{{ __('admin_settings.Enable notifications for missing desiderata') }}"
                        />
                    </div>
                    
                    <div x-show="$wire.notificationsDesiderataEnabled">
                        <flux:field>
                            <flux:label>{{ __('admin_settings.Send notification after X days from semester start') }}</flux:label>
                            <flux:input 
                                type="number" 
                                wire:model="notificationsDesiderataDaysAfter"
                                min="1" 
                                max="20"
                            />
                            @error('notificationsDesiderataWeeksAfter') 
                                <flux:error>{{ $message }}</flux:error> 
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Session Consultations -->
                <div class="space-y-4">
                    <flux:heading size="md" level="3">{{ __('admin_settings.Session consultations') }}</flux:heading>
                    
                    <div>
                        <flux:checkbox 
                            wire:model.live="notificationsSessionConsultationsEnabled"
                            label="{{ __('admin_settings.Enable notifications for missing session consultations') }}"
                        />
                    </div>
                    
                    <div x-show="$wire.notificationsSessionConsultationsEnabled">
                        <flux:field>
                            <flux:label>{{ __('admin_settings.Send notification after X days from session start') }}</flux:label>
                            <flux:input 
                                type="number" 
                                wire:model="notificationsSessionConsultationsDaysAfter"
                                min="1" 
                                max="20"
                            />
                            @error('notificationsSessionConsultationsWeeksAfter') 
                                <flux:error>{{ $message }}</flux:error> 
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Weekly Summary -->
                <div class="space-y-4">
                    <flux:heading size="md" level="3">{{ __('admin_settings.Weekly summary') }}</flux:heading>
                    
                    <div>
                        <flux:checkbox 
                            wire:model="notificationsWeeklySemesterSummaryEnabled"
                            label="{{ __('admin_settings.Enable weekly summary emails for administrators') }}"
                        />
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <flux:button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">
                        {{ __('admin_settings.Save') }}
                    </span>
                    <span wire:loading wire:target="save">
                        {{ __('admin_settings.Saving...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </form>
</div> 