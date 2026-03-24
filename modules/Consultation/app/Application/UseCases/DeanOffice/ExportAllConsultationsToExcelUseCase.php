<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\DeanOffice;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Domain\Enums\ConsultationType;
use Modules\Consultation\Application\Exports\AllConsultationsExport;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ExportAllConsultationsToExcelUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
    ) {
    }

    public function execute(int $semesterId, string $type): BinaryFileResponse
    {
        $consultationType = ConsultationType::tryFrom($type);

        if (!$consultationType || !in_array($consultationType, [ConsultationType::Semester, ConsultationType::Session])) {
            throw ValidationException::withMessages(['type' => 'Invalid consultation type provided for Excel export.']);
        }

        $excludeInactiveForActiveSemester = $this->isExportForActiveSemester($semesterId);

        $scientificWorkers = $this->consultationRepository->getAllScientificWorkersWithConsultations(
            $semesterId,
            $consultationType,
            $excludeInactiveForActiveSemester,
        );

        $filename = 'raport_konsultacji_' . $type . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new AllConsultationsExport($scientificWorkers, $consultationType),
            $filename,
        );
    }

    private function isExportForActiveSemester(int $semesterId): bool
    {
        return $this->getActiveConsultationSemesterUseCase->execute()?->id === $semesterId;
    }
}
