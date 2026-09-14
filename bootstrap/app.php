<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\CheckUserSubscription;
use App\Http\Middleware\CheckUserActive;
use App\Http\Middleware\LicenseEnforce;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->use([
            LicenseEnforce::class,
        ]);

        $middleware->alias([
            'admin'           => IsAdmin::class,
            'role'            => RoleMiddleware::class,
            'guest'           => RedirectIfAuthenticated::class,
            'sub'             => CheckUserSubscription::class,
            'active'          => CheckUserActive::class,
            'installer.check' => \App\Http\Middleware\InstallerMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('admin/*')) {
                return redirect()->guest(route('admin.login'));
            }
            return redirect()->guest(route('login'));
        });
    })
    ->create();
