<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Domain\Enums\RoleEnum;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use InvalidArgumentException;

final class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): ResponseAlias
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowedRoles = $this->convertRolesToEnums($roles);

        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return $this->handleUnauthorizedAccess($request, $user->role);
    }

    private function convertRolesToEnums(array $roles): array
    {
        return array_map(function (string $role): RoleEnum {
            return match (mb_strtolower($role)) {
                'scientific_worker', 'scientific-worker' => RoleEnum::SCIENTIFIC_WORKER,
                'dean_office_worker', 'dean-office-worker' => RoleEnum::DEAN_OFFICE_WORKER,
                'administrator', 'admin' => RoleEnum::ADMINISTRATOR,
                default => throw new InvalidArgumentException("Nieznana rola: {$role}")
            };
        }, $roles);
    }

    private function handleUnauthorizedAccess(Request $request, RoleEnum $userRole): RedirectResponse
    {
        if ($request->expectsJson()) {
            abort(403, __('auth.unauthorized_access'));
        }

        $redirectRoute = match ($userRole) {
            RoleEnum::SCIENTIFIC_WORKER => 'scientific-worker-dashboard',
            RoleEnum::DEAN_OFFICE_WORKER, RoleEnum::ADMINISTRATOR => 'admin-dean-dashboard',
            default => 'dashboard'
        };

        return redirect()->route($redirectRoute)
            ->with('error', __('auth.insufficient_permissions'));
    }
}
