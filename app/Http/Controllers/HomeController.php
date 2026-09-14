<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Setting;
use App\Models\Currency;
use App\Models\HomepageContent;

class HomeController extends Controller
{
    public function index()
    {
        $global = view()->shared('global');
        $homepage = HomepageContent::first();

        $plans = SubscriptionPlan::query()
            ->when(!($global['allow_label_registration'] ?? false), fn($query) => $query->where('role', '!=', 'label'))
            ->paginate(10);

        $siteName = $global['site_name'] ?? config('app.name');

        $selectedPartners = ['Spotify', 'Apple Music', 'Amazon Music', 'YouTube Music', 'Boomplay', 'TikTok', 'Anghami', 'Deezer', 'Tidal', 'Facebook'];
        $partners = array_filter(
            config('stores'),
            fn($key) => in_array($key, $selectedPartners),
            ARRAY_FILTER_USE_KEY
        );

        $count = $plans->count();
        $gridClass = match($count) {
            1 => 'max-w-md mx-auto',
            2 => 'grid md:grid-cols-2 gap-8 max-w-5xl mx-auto',
            default => 'grid md:grid-cols-3 gap-8 max-w-7xl mx-auto',
        };

        $planFeatures = $plans->mapWithKeys(fn($plan) => [
            $plan->id => array_map('trim', preg_split('/[✓,]/', $plan->description, -1, PREG_SPLIT_NO_EMPTY)),
        ]);

        $features = collect($homepage?->features ?? [])
            ->map(fn($feature) => [
                ...$feature,
                'text' => str_replace('{site_name}', $siteName, $feature['text'] ?? ''),
            ])
            ->all();

        $artists = collect($homepage?->artists ?? [])
            ->map(fn($artist) => [
                ...$artist,
                'name' => strtoupper($artist['name'] ?? ''),
            ])
            ->all();

        return view('home', compact('plans', 'homepage', 'partners', 'gridClass', 'planFeatures', 'features', 'artists'));
    }
    
    public function privacy()
    {
        $privacyPolicy = Setting::getValue('privacy_policy', '');
        return view('privacy', compact('privacyPolicy'));
    }

    public function terms()
    {
        $terms = Setting::getValue('terms_pdf', '');
        return view('terms', compact('terms'));
    }

    public function cookies()
    {
        $cookies = Setting::getValue('cookies', '');
        return view('cookies', compact('cookies'));
    }

    public function refund()
    {
        $refund = Setting::getValue('refund_policy', '');
        return view('refund', compact('refund'));
    }
}
