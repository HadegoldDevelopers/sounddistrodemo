<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Setting;

class CheckUserSubscription
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Admins are always allowed
        if ($user->is_admin ?? false) {
            return $next($request);
        }

        // Check if subscription requirement is enabled in settings
        $requireSubscription = Setting::getValue('require_subscription', 0);

        // If subscription is NOT required, skip checks
        if (!$requireSubscription) {
            return $next($request);
        }

        // 1. Check if a plan exists for this user's role
        $plan = SubscriptionPlan::where('role', $user->role)->first();

        if (!$plan) {
            $user->update(['is_sub' => 0]);
            return redirect()->route('payment.index')
                ->with('info', 'A subscription plan is required for your account.');
        }

        // 2. Check if user has an active subscription
        if (!$user->is_sub) {
            return redirect()->route('payment.index')
                ->with('info', 'Please subscribe to unlock all features.');
        }

        // 3. Check if subscription expired
        if ($user->sub_expires_at && now()->greaterThan($user->sub_expires_at)) {
            $user->update(['is_sub' => 0]);
            return redirect()->route('payment.index')
                ->with('info', 'Your subscription has expired.');
        }

        return $next($request);
    }
}