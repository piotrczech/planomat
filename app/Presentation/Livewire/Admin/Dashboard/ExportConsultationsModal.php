<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;

class ExportConsultationsModal extends Component
{
    public bool $showModal = false;

    public ?int $semesterId = null;

    #[On('openConsultationExportModal')]
    public function openModal(?int $semesterId): void
    {
        if (!$semesterId) {
            $this->closeModal();

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

        if (in_array($type, ['semester', 'session'])) {
            return redirect()->route('consultations.dean-office.export.all-consultations.pdf', [
                'semester' => $semesterToExport,
                'type' => $type,
            ]);
        }

        if (in_array($type, ['semester_excel', 'session_excel'])) {
            $realType = str_replace('_excel', '', $type);

            return redirect()->route('consultations.dean-office.export.all-consultations.excel', [
                'semester' => $semesterToExport,
                'type' => $realType,
            ]);
        }

        if (in_array($type, ['unfilled_semester', 'unfilled_session'])) {
            $realType = str_replace('unfilled_', '', $type);

            return redirect()->route('consultations.dean-office.export.unfilled-consultations.pdf', [
                'semester' => $semesterToExport,
                'type' => $realType,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard.export-consultations-modal');
    }
}
