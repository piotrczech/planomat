<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;

class ExportDesiderataModal extends Component
{
    public bool $showModal = false;

    public ?int $semesterId = null;

    #[On('openDesiderataExportModal')]
    public function openModal(?int $semesterId): void
    {
        if (!$semesterId) {
            return;
        }

        $this->semesterId = $semesterId;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->semesterId = null;
    }

    public function export(string $type)
    {
        $semesterToExport = $this->semesterId;
        $this->closeModal();

        if (!$semesterToExport) {
            return;
        }

        if ($type === 'all') {
            return redirect()->route('desiderata.dean-office.export.all-desiderata.pdf', [
                'semester' => $semesterToExport,
            ]);
        }

        if ($type === 'unfilled') {
            return redirect()->route('desiderata.dean-office.export.unfilled-desiderata.pdf', [
                'semester' => $semesterToExport,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard.export-desiderata-modal');
    }
}
