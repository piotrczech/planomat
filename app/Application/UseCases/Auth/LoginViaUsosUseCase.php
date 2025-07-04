<?php

declare(strict_types=1);

namespace App\Application\UseCases\Auth;

use App\Domain\Dto\ExternalAuthUserDto;
use App\Domain\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final readonly class LoginViaUsosUseCase
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    /**
     * @throws AuthenticationException
     */
    public function execute(ExternalAuthUserDto $dto): User
    {
        session(['logged_via_usos' => true]);

        $user = $this->users->findByEmail($dto->email);

        if (!$user) {
            Log::warning('USOS login failed – email not found', ['email' => $dto->email]);

            throw new AuthenticationException(__('app.auth.not_registered'));
        }

        if ($user->usos_id !== $dto->id) {
            $user->forceFill([
                'usos_id' => $dto->id,
            ])->save();

            Log::info('USOS ID updated for user', ['user_id' => $user->id, 'usos_id' => $dto->id]);
        }

        if (!$user->name) {
            $user->forceFill(['name' => $dto->fullName() ?: $user->email])->save();
        }

        Auth::login($user);

        return $user;
    }
}
