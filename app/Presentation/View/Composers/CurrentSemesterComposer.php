<?php

declare(strict_types=1);

namespace App\Presentation\View\Composers;

use App\Infrastructure\Models\Semester;
use Illuminate\View\View;

final class CurrentSemesterComposer
{
    public function compose(View $view): void
    {
        $view->with('currentSemester', Semester::getCurrentSemester());
    }
}
