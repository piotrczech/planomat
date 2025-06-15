<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Enums\RoleEnum;
use App\Infrastructure\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class DashboardController extends Controller
{
    /**
     * Redirect user to appropriate dashboard based on their role
     */
    public function __invoke(): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $redirectRoute = match ($user->role) {
            RoleEnum::ADMINISTRATOR, RoleEnum::DEAN_OFFICE_WORKER => 'admin-dean-dashboard',
            RoleEnum::SCIENTIFIC_WORKER => 'scientific-worker-dashboard',
            default => 'login'
        };

        return redirect()->route($redirectRoute);
    }
}
