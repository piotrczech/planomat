<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use App\Domain\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequireCurrentSemesterMiddleware
{
    public function __construct(
        private readonly GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
        private readonly GetActiveDesiderataSemesterUseCase $getActiveDesiderataSemesterUseCase,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasRole(RoleEnum::SCIENTIFIC_WORKER)) {
            return $next($request);
        }

        $consultationSemester = $this->getActiveConsultationSemesterUseCase->execute();
        $desiderataSemester = $this->getActiveDesiderataSemesterUseCase->execute();

        if (!$consultationSemester || !$desiderataSemester) {
            if ($request->expectsJson()) {
                abort(403, __('auth.no_current_semester_functionality_unavailable'));
            }

            return redirect()->route('scientific-worker-dashboard')
                ->with('error', __('auth.no_current_semester_functionality_unavailable'));
        }

        return $next($request);
    }
}
