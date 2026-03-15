<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

final class AccountSuspendedController
{
    public function __invoke(): Response|View
    {
        $suspended = Session::get('logged_inactive_account', false);

        if (!$suspended) {
            return redirect()->route('usos.login');
        }

        return view('auth.account-suspended');
    }
}
