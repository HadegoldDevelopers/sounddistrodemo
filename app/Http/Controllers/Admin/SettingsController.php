<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use App\Models\Setting;
use App\Models\Currency;
use App\Services\SitemapGenerator;

class SettingsController extends Controller
{

    public function general()
    {
        $siteTitle = Setting::getValue('site_name');
        // Load all needed settings
        $settings = [
            'site_name' => Setting::getValue('site_name'),
            'site_title' => Setting::getValue('site_title', ''),
            'site_tagline' => Setting::getValue('site_tagline'),
            'site_logo' => Setting::getValue('site_logo', ''),
            'favicon' => Setting::getValue('favicon', ''),
            'site_url' => Setting::getValue('site_url', 'https://www.beatwave.com'),
            'force_https' => Setting::getValue('force_https'),
            'allow_user_registration' => Setting::getValue('allow_user_registration', '1'),
            'enable_email_verification' => Setting::getValue('enable_email_verification', '1'),
            'allow_label_registration' => Setting::getValue('allow_label_registration', '1'),
            'require_subscription' => Setting::getValue('require_subscription', '1'),
            'meta_keywords_default' => Setting::getValue('meta_keywords_default', ''),
            'meta_description_default' => Setting::getValue('meta_description_default', ''),
            'google_site_verification' => Setting::getValue('google_site_verification', ''),
            'robots_txt' => Setting::getValue('robots_txt', "User-agent: *\nDisallow: /admin/\nAllow: /"),
            'contact_email' => Setting::getValue('contact_email', ''),
            'contact_phone' => Setting::getValue('contact_phone', ''),
            'contact_address' => Setting::getValue('contact_address', ''),
            'facebook' => Setting::getValue('facebook'),
            'twitter' => Setting::getValue('twitter'),
            'instagram' => Setting::getValue('instagram'),
            'linkedin' => Setting::getValue('linkedin'),
            
            
        ];

        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_title' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,ico|max:1024',
            'site_url' => 'required|url|max:255',
            'force_https' => 'required|boolean',
            'allow_user_registration' => 'required|boolean',
            'enable_email_verification' => 'required|boolean',
            'allow_label_registration' => 'required|boolean',
            'require_subscription' => 'required|boolean',
            'meta_description_default' => 'nullable|string|max:500',
            'meta_keywords_default' => 'nullable|string|max:500',
            'google_site_verification' => 'nullable|string|max:255',
            'robots_txt' => 'nullable|string',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'contact_address' => 'nullable|string|max:255',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
        ]);
        
        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $logoPath = $request->file('site_logo');
            $logoName = time() . '.' . $logoPath->getClientOriginalExtension();
            $logoPath->move(public_path('uploads/site'), $logoName);
            Setting::setValue('site_logo', '/public/uploads/site/' . $logoName);
            Cache::forget('global_site_logo');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon');
            $faviconName = time() . '.' . $faviconPath->getClientOriginalExtension();
            $faviconPath->move(public_path('uploads/favicon'), $faviconName);
            Setting::setValue('favicon', 'public/uploads/favicon/' . $faviconName);
            Cache::forget('global_favicon');
        }
        
        if (isset($validated['force_https'])) {
    updateEnv('FORCE_HTTPS', $validated['force_https']);
}

if (isset($validated['site_name'])) {
    updateEnv('APP_NAME', $validated['site_name']);
}

if (isset($validated['site_url'])) {
    updateEnv('APP_URL', $validated['site_url']);
}


        // Save all other settings
        foreach ($validated as $key => $value) {
            if (!in_array($key, ['site_logo', 'favicon'])) {
                Setting::setValue($key, $value);
                Cache::forget('global_site_settings');
            }
            
          
        }
        
        SitemapGenerator::generate();  

        return redirect()->route('admin.settings.general')->with('success', 'General settings updated successfully.');
    }
    

    public function editTerms()
    {
        $termsPdf = Setting::getValue('terms_pdf');

        return view('admin.settings.terms', compact('termsPdf'));
    }

    public function updateTerms(Request $request)
    {
        $request->validate([
            'terms_pdf' => 'nullable|mimes:pdf|max:5120',
        ]);
        
        // Handle terms_pdf upload
        if ($request->hasFile('terms_pdf')) {
            $termsFile = $request->file('terms_pdf');
            $termsName = 'terms_' . time() . '.pdf';
            $termsFile->move(public_path('uploads/terms'), $termsName);
            
            Setting::setValue('terms_pdf', 'public/uploads/terms/' . $termsName);
        }
        

        return back()->with('success', 'Terms & Conditions updated successfully.');
    }
    
    public function currencyIndex()
{
    $currencies = Currency::orderBy('name', 'asc')
                    ->paginate(20);

    return view('admin.settings.currencies', compact('currencies'));
}

    public function storeCurrency(Request $request)
    {
        $request->validate([
            'code' => 'required|max:3|unique:currencies,code',
            'name' => 'required',
            'symbol' => 'required',
            'country' => 'nullable',
            'cf_code' => 'required',
            'conversion_rate' => 'required|numeric',
        ]);

        Currency::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'symbol' => $request->symbol,
            'country' => $request->country,
            'cf_code' => $request->cf_code,
            'conversion_rate' => $request->conversion_rate,
            'is_active' => $request->is_active ? 1 : 0
        ]);

        return redirect()->route('admin.settings.currencies')
            ->with('success', 'Currency added successfully');
    }


    public function updateCurrency(Request $request, Currency $currency)
    {
        $request->validate([
            'code' => 'required|max:3|unique:currencies,code,' . $currency->id,
            'name' => 'required',
            'symbol' => 'required',
            'country' => 'nullable',
            'cf_code' => 'required',
            'conversion_rate' => 'required|numeric',
        ]);

        $currency->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'symbol' => $request->symbol,
            'country' => $request->country,
            'cf_code' => $request->cf_code,
            'conversion_rate' => $request->conversion_rate,
            'is_active' => $request->is_active ? 1 : 0
        ]);

        return redirect()->route('admin.settings.currencies')
            ->with('success', 'Currency updated successfully');
    }


    public function destroyCurrency(Currency $currency)
    {
        $currency->delete();

        return redirect()->route('admin.settings.currencies')
            ->with('success', 'Currency deleted successfully');
    }
    
    public function editPrivacyPolicy()
{
    $privacyPolicy = Setting::getValue('privacy_policy', '');
    return view('admin.settings.privacy-policy', compact('privacyPolicy'));
}

public function updatePrivacyPolicy(Request $request)
{ 
    $request->validate([
        
    'privacy_policy' => 'nullable|string',
]);

if (empty(strip_tags($request->privacy_policy))) {
    return back()->withErrors(['privacy_policy' => 'Privacy policy content cannot be empty.']);
}

    Setting::setValue('privacy_policy', $request->privacy_policy);
    Cache::forget("global_privacy_policy");

    return back()->with('success', 'Privacy Policy updated successfully.');
}
public function editCookies()
{
    $cookies = Setting::getValue('cookies', '');
    return view('admin.settings.cookies', compact('cookies'));
}

public function updateCookies(Request $request)
{
    $request->validate([
        'cookies' => 'nullable|string',
    ]);

    Setting::setValue('cookies', $request->input('cookies'));

    Cache::forget('global_site_settings');

    return back()->with('success', 'Cookie Policy updated successfully.');
}

public function editRefund()
{
    $refund = Setting::getValue('refund_policy', '');
    return view('admin.settings.refund', compact('refund'));
}

public function updateRefund(Request $request)
{
    $request->validate([
        'refund_policy' => 'nullable|string',
    ]);

    Setting::setValue('refund_policy', $request->refund_policy);
    Cache::forget('global_site_settings');

    return back()->with('success', 'Refund Policy updated successfully.');
}

}
