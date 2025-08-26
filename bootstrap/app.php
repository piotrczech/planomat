<?php

declare(strict_types=1);

use App\Presentation\Http\Middleware\AdminDeanMiddleware;
use App\Presentation\Http\Middleware\AdminOnlyMiddleware;
use App\Presentation\Http\Middleware\RequireCurrentSemesterMiddleware;
use App\Presentation\Http\Middleware\RoleMiddleware;
use App\Presentation\Http\Middleware\ScientificWorkerMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'scientific.worker' => ScientificWorkerMiddleware::class,
            'admin.dean' => AdminDeanMiddleware::class,
            'admin.only' => AdminOnlyMiddleware::class,
            'require.semester' => RequireCurrentSemesterMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

    })->create();
