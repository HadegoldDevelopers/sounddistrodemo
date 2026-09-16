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

// ── Bootstrap .env guard ─────────────────────────────────────────────
// On a fresh install there may be no .env (and therefore no APP_KEY).
// Without an APP_KEY, session cookies can't be encrypted, so CSRF tokens
// never validate and the installer's "Test Connection" AJAX returns a
// 419 (rendered as HTML) — surfacing as "Request failed" in the browser.
// Create .env from .env.example and set an APP_KEY BEFORE the framework
// reads configuration, so every request runs with a valid key.
$envPath        = dirname(__DIR__) . '/.env';
$envExamplePath = dirname(__DIR__) . '/.env.example';

if (!file_exists($envPath)) {
    if (file_exists($envExamplePath)) {
        copy($envExamplePath, $envPath);
    } else {
        file_put_contents($envPath, "APP_KEY=\n");
    }
}

$envContents = (string) file_get_contents($envPath);

if (!preg_match('/^APP_KEY=.+/m', $envContents)) {
    $appKey = 'base64:' . base64_encode(random_bytes(32));

    if (preg_match('/^APP_KEY=.*$/m', $envContents)) {
        $envContents = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $appKey, $envContents);
    } else {
        $envContents .= "\nAPP_KEY=" . $appKey . "\n";
    }

    file_put_contents($envPath, $envContents);
}

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
