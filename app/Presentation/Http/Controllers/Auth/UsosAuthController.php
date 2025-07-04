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

        try {
            $redirect = Socialite::driver('keycloak')->redirect();

            return $redirect;
        } catch (Throwable $e) {
            Log::error('USOS redirect error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login', ['error' => rawurlencode('Nie można połączyć się z usługą USOS. Spróbuj ponownie później.')]);
        }
    }

    public function callback(LoginViaUsosUseCase $useCase): RedirectResponse
    {
        try {
            Log::debug('USOS callback - request query', request()->query());

            $socialUser = Socialite::driver('keycloak')->user();
        } catch (Throwable $e) {
            return redirect()->route('login');
        }

        $dto = new ExternalAuthUserDto(
            id: (string) $socialUser->getId(),
            email: (string) $socialUser->getEmail(),
            firstName: (string) ($socialUser->user['given_name'] ?? ''),
            lastName: (string) ($socialUser->user['family_name'] ?? ''),
        );

        Log::debug('Logged via USOS', $dto->toArray());

        try {
            $useCase->execute($dto);

            return redirect()->intended(route('dashboard'));
        } catch (AuthenticationException $e) {
            Auth::logout();

            return redirect()->route('account.pending');
        }
    }
}
