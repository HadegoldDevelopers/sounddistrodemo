@extends('layouts.admin.app')

@section('title', 'General Settings')

@section('content') 

<div class="text-gray-900">

    <h1 class="text-3xl font-bold mb-6">General Settings</h1>

   

    {{-- Tabs --}}
    <div class="mb-6">
        @php
            $tabs = [
                'site' => 'Site Info',
                'branding' => 'Branding',
                'security' => 'URL & Security',
                'seo' => 'SEO & Metadata',
                'contact' => 'Contact Info',
                'others' => 'Others',
            ];
            $activeTab = old('active_tab', 'site');
        @endphp

        <!-- Mobile Tabs -->
        <div class="md:hidden mb-4">
            <select id="mobileTabSelect" onchange="switchTabFromSelect(this)" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                @foreach($tabs as $key => $label)
                    <option value="{{ $key }}" {{ $activeTab === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Desktop Tabs -->
        <div class="hidden md:flex space-x-2 border-b border-gray-300">
            @foreach($tabs as $key => $label)
                <button
                    type="button"
                    role="tab"
                    aria-selected="{{ $activeTab === $key ? 'true' : 'false' }}"
                    aria-controls="tab-panel-{{ $key }}"
                    id="tab-{{ $key }}"
                    class="py-2 px-4 font-semibold border-b-2 transition-colors
                    {{ $activeTab === $key ? 'border-orange-500 text-orange-500' : 'border-transparent text-gray-600 hover:text-orange-500' }}"
                    onclick="switchTab('{{ $key }}')"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="settingsForm">
        @csrf
        <input type="hidden" name="active_tab" id="active_tab" value="{{ $activeTab }}">

        {{-- Site Info --}}
        <section id="tab-panel-site" role="tabpanel" aria-labelledby="tab-site" class="{{ $activeTab === 'site' ? '' : 'hidden' }}">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Website Information</h2>

                <div class="mb-4">
                    <label for="site_name" class="block font-medium mb-1">Website Name <span class="text-red-500">*</span></label>
                    <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                    @error('site_name')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label for="site_title" class="block font-medium mb-1">Website Title</label>
                    <input type="text" name="site_title" id="site_title" value="{{ old('site_title', $settings['site_title'] ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @error('site_title')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="site_tagline" class="block font-medium mb-1">Site Tagline</label>
                    <input type="text" name="site_tagline" id="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @error('site_tagline')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        {{-- Branding --}}
        <section id="tab-panel-branding" role="tabpanel" aria-labelledby="tab-branding" class="{{ $activeTab === 'branding' ? '' : 'hidden' }}">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Branding & Appearance</h2>

                {{-- Logo --}}
                <div class="mb-6">
                    <label class="block font-medium mb-2">Current Site Logo</label>
                    @if(!empty($settings['site_logo']))
                        <img src="{{ asset($settings['site_logo']) }}" alt="Site Logo" class="h-20 mb-2 rounded-lg border border-gray-300">
                    @else
                        <p class="text-gray-400 italic">No logo uploaded yet.</p>
                    @endif

                    <label for="site_logo" class="block font-medium mt-4 mb-1">Upload New Site Logo</label>
                    <input type="file" name="site_logo" id="site_logo" accept="image/png, image/jpeg" class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:bg-orange-500 file:text-white hover:file:bg-orange-600" onchange="previewImage(event, 'logoPreview')">
                    @error('site_logo')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror

                    <img id="logoPreview" src="#" alt="Logo Preview" class="mt-4 hidden h-20 rounded-lg border border-gray-300">
                </div>

                {{-- Favicon --}}
                <div>
                    <label class="block font-medium mb-2">Current Favicon</label>
                    @if(!empty($settings['favicon']))
                        <img src="{{ asset($settings['favicon']) }}" alt="Favicon" class="h-10 w-10 mb-2 rounded border border-gray-300">
                    @else
                        <p class="text-gray-400 italic">No favicon uploaded yet.</p>
                    @endif

                    <label for="favicon" class="block font-medium mt-4 mb-1">Upload New Favicon</label>
                    <input type="file" name="favicon" id="favicon" accept=".ico,image/png,image/jpeg" class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:bg-orange-500 file:text-white hover:file:bg-orange-600" onchange="previewImage(event, 'faviconPreview')">
                    @error('favicon')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror

                    <img id="faviconPreview" src="#" alt="Favicon Preview" class="mt-4 hidden h-10 w-10 rounded border border-gray-300">
                </div>
            </div>
        </section>

        {{-- URL & Security --}}
        <section id="tab-panel-security" role="tabpanel" aria-labelledby="tab-security" class="{{ $activeTab === 'security' ? '' : 'hidden' }}">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">URL & Security</h2>

                <div class="mb-4">
                    <label for="site_url" class="block font-medium mb-1">Site URL <span class="text-red-500">*</span></label>
                    <input type="url" name="site_url" id="site_url" value="{{ old('site_url', $settings['site_url'] ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                    @error('site_url')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center space-x-3">
                    <input type="hidden" name="force_https" value="0">
<input 
    type="checkbox" 
    name="force_https" 
    id="force_https" 
    value="1" 
    @checked(old('force_https', $settings['force_https'] ?? 0)) 
    class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-2 focus:ring-orange-500">
<label for="force_https" class="font-medium">Force HTTPS Redirect</label>

                </div>
            </div>
        </section>

        {{-- Others --}}
        <section id="tab-panel-others" role="tabpanel" aria-labelledby="tab-others" class="{{ $activeTab === 'Others' ? '' : 'hidden' }}">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">User & Registration Settings</h2>

              
                <div class="flex items-center space-x-3 mb-4">
    <input type="hidden" name="allow_user_registration" value="0">
    <input type="checkbox" name="allow_user_registration" id="allow_user_registration" value="1"
        {{ old('allow_user_registration', $settings['allow_user_registration'] ?? false) ? 'checked' : '' }}
        class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-2 focus:ring-orange-500">
    <label for="allow_user_registration" class="font-medium">Allow User Registration</label>
</div>

                 <div class="flex items-center space-x-3 mb-4">
            <input type="hidden" name="allow_label_registration" value="0">
                    <input type="checkbox" name="allow_label_registration" id="allow_label_registration" value="1" {{ old('allow_label_registration', $settings['allow_label_registration'] ?? false) ? 'checked' : '' }} class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-2 focus:ring-orange-500">
                    <label for="allow_label_registration" class="font-medium">Allow to registration from Labels</label>
                
                </div>

                <div class="flex items-center space-x-3">
                    <input type="hidden" name="enable_email_verification" value="0">
                    <input type="checkbox" name="enable_email_verification" id="enable_email_verification" value="1" {{ old('enable_email_verification', $settings['enable_email_verification'] ?? false) ? 'checked' : '' }} class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-2 focus:ring-orange-500">
                    <label for="enable_email_verification" class="font-medium">Enable Email Verification</label>
                </div>
        <h2 class="text-xl font-semibold mb-4">User Access & Subscription Settings</h2>
<div class="flex items-center space-x-3 mb-4">
    <input type="hidden" name="require_subscription" value="0">
    <input type="checkbox" name="require_subscription" id="require_subscription" value="1"
        @checked(old('require_subscription', $settings['require_subscription'] ?? false))
        class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-2 focus:ring-orange-500">
    <label for="require_subscription" class="font-medium">
        Require Subscription to Upload Music
    </label>
</div>
<p class="text-gray-500 text-sm mb-4">
    Enable this if users must subscribe before uploading music. Leave unchecked for free uploads.
</p>
<h2 class="text-xl font-semibold mb-4">Social Media Links</h2>

<div class="mb-4">
    <label for="facebook" class="block font-medium mb-1">Facebook URL</label>
    <input type="url" name="facebook" id="facebook" value="{{ old('facebook', $settings['facebook'] ?? '') }}"
        placeholder="https://facebook.com/yourpage"
        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
</div>

<div class="mb-4">
    <label for="twitter" class="block font-medium mb-1">Twitter URL</label>
    <input type="url" name="twitter" id="twitter" value="{{ old('twitter', $settings['twitter'] ?? '') }}"
        placeholder="https://twitter.com/yourhandle"
        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
</div>

<div class="mb-4">
    <label for="instagram" class="block font-medium mb-1">Instagram URL</label>
    <input type="url" name="instagram" id="instagram" value="{{ old('instagram', $settings['instagram'] ?? '') }}"
        placeholder="https://instagram.com/yourhandle"
        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
</div>

<div class="mb-4">
    <label for="linkedin" class="block font-medium mb-1">LinkedIn URL</label>
    <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin', $settings['linkedin'] ?? '') }}"
        placeholder="https://linkedin.com/company/yourcompany"
        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
</div>

<div class="mb-4">
    <label for="footer_text" class="block font-medium mb-1">Footer Text</label>
    <input type="text" name="footer_text" id="footer_text" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}"
        placeholder="Made for creators, by creators."
        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
    <small class="text-gray-400">Shown at the bottom-right of the site footer.</small>
</div>

            </div>
        </section>

        {{-- SEO & Metadata --}}
        <section id="tab-panel-seo" role="tabpanel" aria-labelledby="tab-seo" class="{{ $activeTab === 'seo' ? '' : 'hidden' }}">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">SEO & Metadata</h2>

                

                <div class="mb-4">
                    <label for="meta_keywords" class="block font-medium mb-1">Meta Keywords</label>
                    <input type="text" name="meta_keywords_default" id="meta_keywords_default" value="{{ old('meta_keywords', $settings['meta_keywords_default'] ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <small class="text-gray-400">Comma-separated keywords</small>
                    @error('meta_keywords')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="meta_description_default" class="block font-medium mb-1">Meta Description</label>
                    <textarea name="meta_description_default" id="meta_description_default" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('meta_description_default', $settings['meta_description_default'] ?? '') }}</textarea>
                    @error('meta_description_default')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
                </div>

               <div class="mb-4">
  <label for="google_site_verification" class="block font-medium mb-1">
    Google Site Verification
  </label>

  <p class="text-sm text-gray-500 mb-2">
    Get this code from
    <a
      href="https://search.google.com/search-console"
      target="_blank"
      rel="noopener noreferrer"
      class="text-orange-600 hover:underline font-medium"
    >
      Google Search Console
    </a>.
    Copy only the value inside <code>content=""</code> from the meta tag.
  </p>

  <input
    type="text"
    name="google_site_verification"
    id="google_site_verification"
    value="{{ old('google_site_verification', $settings['google_site_verification'] ?? '') }}"
    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
  >

  @error('google_site_verification')
    <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
  @enderror
</div>

            </div>
        </section>

        {{-- Contact Info --}}
        <section id="tab-panel-contact" role="tabpanel" aria-labelledby="tab-contact" class="{{ $activeTab === 'contact' ? '' : 'hidden' }}">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Contact Information</h2>

                <div class="mb-4">
                    <label for="contact_email" class="block font-medium mb-1">Contact Email</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @error('contact_email')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="contact_phone" class="block font-medium mb-1">Contact Phone</label>
                    <input type="number" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @error('contact_phone')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="contact_address" class="block font-medium mb-1">Contact Address</label>
                    <textarea name="contact_address" id="contact_address" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                    @error('contact_address')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-lg transition">Save Changes</button>
        </div>

    </form>
</div>

{{-- Tab JS --}}
<script>
    function switchTab(tabKey) {
        document.querySelectorAll('[role="tabpanel"]').forEach(panel => panel.classList.add('hidden'));
        document.querySelectorAll('[role="tab"]').forEach(tab => {
            tab.setAttribute('aria-selected', 'false');
            tab.classList.remove('border-orange-500', 'text-orange-500');
            tab.classList.add('border-transparent', 'text-gray-600', 'hover:text-orange-500');
        });

        document.getElementById('tab-panel-' + tabKey).classList.remove('hidden');
        const activeTab = document.getElementById('tab-' + tabKey);
        activeTab.setAttribute('aria-selected', 'true');
        activeTab.classList.add('border-orange-500', 'text-orange-500');
        activeTab.classList.remove('border-transparent', 'text-gray-600', 'hover:text-orange-500');

        document.getElementById('active_tab').value = tabKey;
    }

    function switchTabFromSelect(select) {
        switchTab(select.value);
    }

    function previewImage(event, previewId) {
        const input = event.target;
        const preview = document.getElementById(previewId);
        if(input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
