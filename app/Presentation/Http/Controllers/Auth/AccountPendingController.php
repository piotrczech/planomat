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
        $pending = Session::get('pending_external_user');

        if (!$pending) {
            return redirect()->route('usos.login');
        }

        return view('account.pending', ['pendingUser' => $pending]);
    }
}
