<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class InstallerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $installed    = File::exists(storage_path('installed'));
        $onFinish     = $request->routeIs('installer.finish');
        $onInstaller  = str_starts_with($request->path(), 'install');

        // Already installed — block installer except finish page
        if ($installed && $onInstaller && !$onFinish) {
            return redirect('/')->with('error', 'Already installed.');
        }

        // Not installed — redirect everything to installer
        if (!$installed && !$onInstaller) {
            return redirect()->route('installer.welcome');
        }

        return $next($request);
    }
}
