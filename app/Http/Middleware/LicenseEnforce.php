<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Global license enforcement.
 *
 *  - Re-verifies the stored license at most once every 24h.
 *  - 1-2 failures   → admin is logged out and the admin area is blocked.
 *  - 3+ failures    → full lockdown: the public site shows a maintenance
 *                     page and the admin area is blocked.
 *  - The admin license page stays reachable so a valid code can restore it.
 */
class LicenseEnforce
{
    public function handle(Request $request, Closure $next)
    {
        if (!file_exists(storage_path('installed'))) {
            return $next($request);
        }

        $path = $request->path();

        if (str_starts_with($path, 'install') ||
            $path === 'admin/license' ||
            $request->routeIs('admin.login') ||
            $request->routeIs('admin.login.submit')) {
            return $next($request);
        }

        $license = app(LicenseService::class);

        try {
            $license->checkNow();
        } catch (\Exception $e) {
            \Log::error('License check failed: ' . $e->getMessage());
        }

        if ($license->isLockedDown()) {
            return response()->view('errors.maintenance', ['reason' => 'license'], 503);
        }

        if ($license->isAdminBlocked() && $request->routeIs('admin.*')) {
            Auth::guard('admin')->logout();

            return redirect()->route('admin.license');
        }

        return $next($request);
    }
}