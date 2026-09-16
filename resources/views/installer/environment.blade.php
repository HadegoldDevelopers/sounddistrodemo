@extends('installer.layout')
@php $currentStep = 3; @endphp
@section('title', 'App Settings')

@section('content')
    <h2 class="card-title">Application Settings</h2>
    <p class="card-subtitle">Configure your application name, URL, and email settings.</p>

    <form method="POST" action="{{ route('installer.environment.save') }}">
        @csrf

        {{-- App Settings --}}
        <div class="section-title">App and Site Settings</div>

        <div class="form-group">
            <label>Site Name <span class="req">*</span></label>
            <input type="text" name="site_name" value="{{ old('site_name', 'Distro Supawave') }}"
                placeholder="My Distro Platform" required>
            <div class="field-hint">This appears in the header, emails, and browser tab.</div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>App URL <span class="req">*</span></label>
                <input type="url" name="site_url" value="{{ old('site_url', request()->getSchemeAndHttpHost()) }}"
                    placeholder="https://yourdomain.com" required>
                <div class="field-hint">Example: https://yourdomain.com</div>
            </div>
            <div class="form-group">
                <label>Contact Email <span class="req">*</span></label>
                <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                    placeholder="support@yourdomain.com" required>
                <div class="field-hint">Used for support contact and system emails.</div>
            </div>
            <div class="form-group">
                <label>Environment <span class="req">*</span></label>
                <select name="app_env">
                    <option value="production" selected>Production</option>
                    <option value="local">Local / Development</option>
                </select>
            </div>
        </div>

        {{-- Mail Settings --}}
        <div class="section-title" style="margin-top:28px;">Email / Mail</div>

        <div class="form-row">
            <div class="form-group">
                <label>Mail Driver <span class="req">*</span></label>
                <select name="mail_driver" id="mailDriver" onchange="toggleMailFields()">
                    <option value="smtp">SMTP</option>
                    <option value="mailgun">Mailgun</option>
                    <option value="ses">Amazon SES</option>
                    <option value="log">Log (Testing only)</option>
                </select>
            </div>
            <div class="form-group">
                <label>From Email</label>
                <input type="email" name="mail_from" value="{{ old('mail_from') }}" placeholder="noreply@yourdomain.com">
            </div>
        </div>

        <div id="smtpFields">
            <div class="form-row">
                <div class="form-group">
                    <label>Mail Host</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', 'smtp.mailtrap.io') }}"
                        placeholder="smtp.gmail.com">
                </div>
                <div class="form-group">
                    <label>Mail Port</label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', '465') }}" placeholder="465">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Mail Username</label>
                    <input type="text" name="mail_user" value="{{ old('mail_user') }}" placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label>Mail Password</label>
                    <input type="password" name="mail_pass" placeholder="••••••••">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>From Name</label>
            <input type="text" name="mail_name" value="{{ old('mail_name') }}" placeholder="Distro Supawave Platform">
            <div class="field-hint">Name that appears in the "From" field of emails sent to users.</div>
        </div>

        <div class="btn-row">
            <a href="{{ route('installer.database') }}" class="btn btn-outline">← Back</a>
            <button type="submit" class="btn btn-primary">Save & Continue →</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        function toggleMailFields() {
            const driver = document.getElementById('mailDriver').value;
            const smtpFields = document.getElementById('smtpFields');
            smtpFields.style.display = (driver === 'log') ? 'none' : 'block';
        }
        toggleMailFields();
    </script>
@endpush