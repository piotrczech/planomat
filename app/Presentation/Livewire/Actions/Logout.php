<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class Logout
{
    public function __invoke()
    {
        $loggedViaUsos = Session::pull('logged_via_usos', false);

        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        if ($loggedViaUsos) {
            /**
             * @var \SocialiteProviders\Keycloak\Provider $provider
             */
            $provider = Socialite::driver('keycloak');
            $logoutUrl = $provider->getLogoutUrl(config('services.keycloak.redirect'), config('services.keycloak.client_id'), null, ['logout' => true]);

            return redirect()->away($logoutUrl);
        }

        return redirect('/');
    }
}
