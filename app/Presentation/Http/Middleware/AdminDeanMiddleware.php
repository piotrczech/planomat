<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Domain\Enums\RoleEnum;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminDeanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasRole(RoleEnum::ADMINISTRATOR) && !$user->hasRole(RoleEnum::DEAN_OFFICE_WORKER)) {
            if ($request->expectsJson()) {
                abort(403, __('auth.admin_dean_access_required'));
            }

            return redirect()->route('dashboard')
                ->with('error', __('auth.admin_dean_access_required'));
        }

        return $next($request);
    }
}
