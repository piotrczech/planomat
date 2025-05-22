<?php

declare(strict_types=1);

namespace Modules\Desiderata\Presentation\Livewire\Dashboard;

use Livewire\Component;

class DesiderataCard extends Component
{
    public array $desiderataItems = [];

    public function mount(): void
    {
        // Przykładowe dane statyczne na podstawie migracji desiderata_table.php
        $this->desiderataItems = [
            [
                'id' => 1,
                'semester_id' => 1, // Przykładowe ID semestru
                'scientific_worker_id' => 1, // Przykładowe ID pracownika
                'want_stationary' => true,
                'want_non_stationary' => false,
                'agree_to_overtime' => true,
                'master_theses_count' => 2,
                'bachelor_theses_count' => 3,
                'max_hours_per_day' => 6,
                'max_consecutive_hours' => 4,
                'additional_notes' => 'Brak dodatkowych uwag.',
                'created_at' => now()->subDays(2)->format('Y-m-d H:i:s'),
                'updated_at' => now()->subDay()->format('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'semester_id' => 1,
                'scientific_worker_id' => 2,
                'want_stationary' => true,
                'want_non_stationary' => true,
                'agree_to_overtime' => false,
                'master_theses_count' => 1,
                'bachelor_theses_count' => 1,
                'max_hours_per_day' => 8,
                'max_consecutive_hours' => 4,
                'additional_notes' => null,
                'created_at' => now()->subDays(5)->format('Y-m-d H:i:s'),
                'updated_at' => now()->subDays(3)->format('Y-m-d H:i:s'),
            ],
        ];
    }

    public function redirectToForm()
    {
        // TODO: Zaimplementować przekierowanie do odpowiedniego formularza/widoku desideratów
        return redirect()->route('dashboard'); // Tymczasowe przekierowanie
    }

    public function render()
    {
        return view('desiderata::dashboard.desiderata-card');
    }
}
