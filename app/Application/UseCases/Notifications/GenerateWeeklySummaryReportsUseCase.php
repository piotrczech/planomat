<?php

declare(strict_types=1);

namespace App\Application\UseCases\Notifications;

use App\Domain\Interfaces\SettingRepositoryInterface;
use Modules\Consultation\Application\Services\ConsultationReportService;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Consultation\Domain\Interfaces\Services\PdfGeneratorInterface;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use App\Application\UseCases\Course\GetAllCoursesUseCase;
use App\Domain\Interfaces\SemesterRepositoryInterface;

final class GenerateWeeklySummaryReportsUseCase
{
    public function __construct(
        private readonly SettingRepositoryInterface $settingRepository,
        private readonly SemesterRepositoryInterface $semesterRepository,
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly DesideratumRepositoryInterface $desideratumRepository,
        private readonly PdfGeneratorInterface $pdfGenerator,
        private readonly ConsultationReportService $reportService,
        private readonly GetAllCoursesUseCase $getAllCoursesUseCase,
        private readonly GetActiveSemesterForDesiderataUseCase $getActiveSemesterForDesiderataUseCase,
        private readonly GetActiveSemesterForConsultationsUseCase $getActiveSemesterForConsultationsUseCase,
    ) {
    }

    public function execute(): array
    {
        $activeSemesterForConsultation = $this->getActiveSemesterForConsultationsUseCase->execute();
        $activeSemesterForDesiderata = $this->getActiveSemesterForDesiderataUseCase->execute();

        if ($activeSemesterForConsultation) {
        }

        // Generate desiderata report
        if ($activeSemesterForDesiderata) {

        }

        return $attachments;
    }
}
