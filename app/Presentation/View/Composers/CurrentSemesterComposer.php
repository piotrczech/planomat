<?php

declare(strict_types=1);

namespace App\Presentation\View\Composers;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use Illuminate\View\View;

final class CurrentSemesterComposer
{
    public function __construct(
        private readonly GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
        private readonly GetActiveDesiderataSemesterUseCase $getActiveDesiderataSemesterUseCase,
    ) {
    }

    public function compose(View $view): void
    {
        $consultationSemester = $this->getActiveConsultationSemesterUseCase->execute();
        $desiderataSemester = $this->getActiveDesiderataSemesterUseCase->execute();

        $view->with('activeConsultationSemester', $consultationSemester)
            ->with('activeDesiderataSemester', $desiderataSemester);
    }
}
