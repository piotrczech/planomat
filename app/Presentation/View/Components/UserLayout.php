<?php

declare(strict_types=1);

namespace App\Presentation\View\Components;

use App\Domain\Enums\RoleEnum;
use App\Infrastructure\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

final class UserLayout extends Component
{
    public function __construct(public ?string $title = null)
    {
    }

    public function render(): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && ($user->hasRole(RoleEnum::ADMINISTRATOR) || $user->hasRole(RoleEnum::DEAN_OFFICE_WORKER))) {
            return view('components.layouts.app.sidebar');
        }

        return view('components.layouts.app.header');
    }
}
