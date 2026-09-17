<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
{
    \Illuminate\Support\Facades\Schema::defaultStringLength(191);

    // If no APP_KEY is present (e.g. a fresh unzip before the installer runs),
    // generate one now so the app can boot and the web installer can load.
    if (empty(config('app.key'))) {
        $key = 'base64:' . base64_encode(random_bytes(32));
        config(['app.key' => $key]);

        $envFile = base_path('.env');

        if (is_file($envFile)) {
            $contents = file_get_contents($envFile);
            $line    = 'APP_KEY=' . $key;

            if (preg_match('/^APP_KEY=.*$/m', $contents)) {
                $contents = preg_replace('/^APP_KEY=.*$/m', $line, $contents);
            } else {
                $contents .= PHP_EOL . $line;
            }

            file_put_contents($envFile, $contents);
        }
    }
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!file_exists(storage_path('installed'))) {
            return;
        }

        // Force HTTPS if enabled
        if (Setting::getValue('force_https', 0)) {
            URL::forceScheme('https');
        }

        // Load global settings from cache
        $global = cache()->remember('global_site_settings', 3600, function () {
            return [
                'site_name' => Setting::getValue('site_name', 'WhiteLabel Distro'),
                'site_title' => Setting::getValue('site_title', ''),
                'site_tag' => Setting::getValue('site_tagline', 'Your music, your vibe'),
                'footer_text' => Setting::getValue('footer_text', 'Made for creators, by creators.'),
                'site_logo' => Setting::getValue('site_logo', ''),
                'favicon' => Setting::getValue('favicon', ''),
                'email' => Setting::getValue('contact_email', ''),
                'phone' => Setting::getValue('contact_phone', ''),
                'address' => Setting::getValue('contact_address', ''),
                'terms_pdf' => Setting::getValue('terms_pdf'),
                'terms' => Setting::getValue('terms', ''),
                'privacy_policy' => Setting::getValue('privacy_policy', ''),

                // Socials
                'facebook' => Setting::getValue('facebook', ''),
                'twitter' => Setting::getValue('twitter', ''),
                'instagram' => Setting::getValue('instagram', ''),
                'linkedin' => Setting::getValue('linkedin', ''),

                // SEO
                'meta_keywords' => Setting::getValue('meta_keywords_default', ''),
                'meta_description' => Setting::getValue('meta_description_default', ''),
                'gsiteverify' => Setting::getValue('google_site_verification', ''),

                // Registration toggles
                'allow_user_registration' => (bool) Setting::getValue('allow_user_registration', 0),
                'allow_label_registration' => (bool) Setting::getValue('allow_label_registration', 0),
                'require_subscription' => (bool) Setting::getValue('require_subscription', 0),
            ];
        });

        // Set dynamic mail sender
        Config::set('mail.from.name', $global['site_name']);

        // Share settings with all views
        View::share('global', $global);
    }
}