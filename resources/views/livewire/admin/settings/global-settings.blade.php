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