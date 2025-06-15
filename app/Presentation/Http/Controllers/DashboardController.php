<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Enums\RoleEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && ($user->hasRole(RoleEnum::ADMINISTRATOR) || $user->hasRole(RoleEnum::DEAN_OFFICE_WORKER))) {
            return view('dashboards.admin-dean');
        }

        return view('dashboards.scientific-worker');
    }
}
