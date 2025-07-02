<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    public function __invoke()
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        $loggedViaUsos = session()->pull('logged_via_usos', false);

        if ($loggedViaUsos) {
            /** @var \SocialiteProviders\Keycloak\Provider $provider */
            $provider = \Laravel\Socialite\Facades\Socialite::driver('keycloak');
            $logoutUrl = $provider->getLogoutUrl(route('login'), config('services.keycloak.client_id'));

            return redirect($logoutUrl);
        }

        return redirect('/');
    }
}
