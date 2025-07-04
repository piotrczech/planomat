<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Auth;

use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

final class AccountPendingController
{
    public function __invoke(): Response|View
    {
        $pending = Session::get('logged_via_usos_no_account', false);

        if (!$pending) {
            return redirect()->route('usos.login');
        }

        return view('auth.account-pending');
    }
}
