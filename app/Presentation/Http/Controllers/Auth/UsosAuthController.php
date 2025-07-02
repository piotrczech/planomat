<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Auth;

use App\Application\UseCases\Auth\LoginViaUsosUseCase;
use App\Domain\Dto\ExternalAuthUserDto;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class UsosAuthController
{
    public function redirect(): RedirectResponse
    {
        $config = config('services.keycloak');

        $required = ['client_id', 'client_secret', 'base_url', 'realms', 'redirect'];

        foreach ($required as $key) {
            if (empty($config[$key])) {
                Log::error('Brak konfiguracji Keycloak', [
                    'missing' => $key,
                    'config' => $config,
                ]);

                abort(500, "Brak konfiguracji USOS – klucz {$key} nie jest ustawiony (sprawdź .env).");
            }
        }

        Log::debug('Starting USOS redirect');

        try {
            $redirect = Socialite::driver('keycloak')->redirect();

            Log::debug('USOS redirect completed');

            return $redirect;
        } catch (Throwable $e) {
            Log::error('USOS redirect error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->with('error', 'Nie można połączyć się z usługą USOS. Spróbuj ponownie później.');
        }
    }

    public function callback(LoginViaUsosUseCase $useCase): RedirectResponse
    {
        try {
            Log::debug('USOS callback - request query', request()->query());

            $socialUser = Socialite::driver('keycloak')->user();
        } catch (Throwable $e) {
            Log::error('USOS callback error during Socialite user fetch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'Błąd podczas pobierania danych z USOS. Szczegóły w logach.');
        }

        Log::debug('USOS/Keycloak raw user', [
            'id' => $socialUser->getId(),
            'email' => $socialUser->getEmail(),
            'raw' => $socialUser->user,
        ]);

        Log::debug('USOS tokens', [
            'token' => $socialUser->token ?? null,
            'refresh_token' => $socialUser->refreshToken ?? null,
            'expires_in' => $socialUser->expiresIn ?? null,
            'approved_scopes' => property_exists($socialUser, 'approvedScopes') ? $socialUser->approvedScopes : null,
            'access_token_response' => property_exists($socialUser, 'accessTokenResponseBody') ? $socialUser->accessTokenResponseBody : null,
        ]);

        $dto = new ExternalAuthUserDto(
            id: (string) $socialUser->getId(),
            email: (string) $socialUser->getEmail(),
            firstName: (string) ($socialUser->user['given_name'] ?? ''),
            lastName: (string) ($socialUser->user['family_name'] ?? ''),
        );

        Log::debug('USOS DTO', $dto->toArray());

        try {
            $useCase->execute($dto);
            session(['logged_via_usos' => true]);

            return redirect()->intended(route('dashboard'));
        } catch (AuthenticationException $e) {
            $message = $e->getMessage();
            Log::error('USOS callback error during login', [
                'message' => $message,
            ]);

            Auth::logout();

            $redirectAfterLogout = route('login', ['error' => rawurlencode($message)]);

            /** @var \SocialiteProviders\Keycloak\Provider $provider */
            $provider = Socialite::driver('keycloak');
            $logoutUrl = $provider->getLogoutUrl($redirectAfterLogout, config('services.keycloak.client_id'));

            return redirect($logoutUrl);
        }
    }
}
