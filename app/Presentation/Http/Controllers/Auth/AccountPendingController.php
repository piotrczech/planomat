<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Auth;

use App\Domain\Dto\ExternalAuthUserDto;
use App\Domain\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Socialite\Facades\Socialite;

final class AccountPendingController
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function __invoke(): Response|View
    {
        try {
            $socialUser = Socialite::driver('keycloak')->user();

            if (!$socialUser) {
                return redirect()->route('login');
            }

            $dto = new ExternalAuthUserDto(
                id: (string) $socialUser->getId(),
                email: (string) $socialUser->getEmail(),
                firstName: (string) ($socialUser->user['given_name'] ?? ''),
                lastName: (string) ($socialUser->user['family_name'] ?? ''),
            );

            $user = $this->users->findByEmail($dto->email);

            if (!$user) {
                return view('account.pending');
            }

            return redirect()->route('dashboard');
        } catch (Exception $e) {
            return redirect()->route('login');
        }
    }
}
