@extends('installer.layout')
@php $currentStep = 6; @endphp
@section('title', 'Installation Complete')

@section('content')
        <div style="text-align:center; padding:16px 0 8px;">

            <h2 class="card-title" style="font-size:26px;">Installation Complete!</h2>
            <p class="card-subtitle" style="font-size:15px; max-width:440px; margin:0 auto 32px;">
                Your platform is ready. Here's a summary of what was set up:
            </p>
    {{-- Login details box --}}
    <div style="background:var(--surface2); border:1px solid var(--primary); border-radius:10px;
                            padding:20px; text-align:left; max-width:480px; margin:0 auto 24px;">
        <div style="font-size:13px; font-weight:600; margin-bottom:14px; color:var(--text);">
            Your Access URLs
        </div>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <div style="font-size:13px;">
                <span style="color:var(--muted); display:inline-block; width:120px;">Homepage</span>
                <a href="{{ route('installer.complete', ['redirect' => 'home']) }}"
                    style="color:var(--primary); word-break:break-all;">
                    {{ config('app.url') }}
                </a>
            </div>
            <div style="font-size:13px;">
                <span style="color:var(--muted); display:inline-block; width:120px;">Admin Panel</span>
                <a href="{{ route('installer.complete') }}" style="color:var(--primary); word-break:break-all;">
                    {{ config('app.url') }}/admin/login
                </a>
            </div>
        </div>
    </div>
            <ul class="finish-list" style="text-align:left; max-width:480px; margin:0 auto 32px;">
                <li>
                    <div class="check-icon">✓</div>
                    <div>
                        <div style="font-weight:600; font-size:14px;">.env File Created</div>
                        <div style="font-size:12px; color:var(--muted);">Database & app config written</div>
                    </div>
                </li>
                <li>
                    <div class="check-icon">✓</div>
                    <div>
                        <div style="font-weight:600; font-size:14px;">Database Migrated</div>
                        <div style="font-size:12px; color:var(--muted);">All tables created successfully</div>
                    </div>
                </li>
                <li>
                    <div class="check-icon">✓</div>
                    <div>
                        <div style="font-weight:600; font-size:14px;">Admin Account Created</div>
                        <div style="font-size:12px; color:var(--muted);">You can now log in to the admin panel</div>
                    </div>
                </li>
                <li>
                    <div class="check-icon">✓</div>
                    <div>
                        <div style="font-weight:600; font-size:14px;">Encryption Key Generated</div>
                        <div style="font-size:12px; color:var(--muted);">APP_KEY set in .env</div>
                    </div>
                </li>
                <li>
                    <div class="check-icon">✓</div>
                    <div>
                        <div style="font-weight:600; font-size:14px;">Default Data Seeded</div>
                        <div style="font-size:12px; color:var(--muted);">Currencies, settings and plans ready</div>
                    </div>
                </li>
            </ul>

            {{-- Next steps --}}
            <div style="background:var(--surface2); border:1px solid var(--border); border-radius:10px;
                            padding:20px; text-align:left; max-width:480px; margin:0 auto 28px;">
                <div style="font-size:13px; font-weight:600; margin-bottom:12px;">🎯 Next Steps</div>
                <ol style="padding-left:16px; line-height:2; font-size:13px; color:var(--muted);">
                    <li>Log in to your <strong style="color:var(--text);">Admin Panel</strong></li>
                    <li>Go to <strong style="color:var(--text);">Settings</strong></li>
                    <li>Customize your <strong style="color:var(--text);">site logo & branding</strong></li>
                    <li>Set up your <strong style="color:var(--text);">subscription plans</strong></li>
                </ol>
            </div>

            {{-- Verify license before continuing --}}
            <div style="background:var(--surface2); border:1px solid var(--primary); border-radius:10px;
                            padding:16px; text-align:center; max-width:480px; margin:0 auto 16px;">
                <div style="font-size:14px; font-weight:700; margin-bottom:8px; color:var(--text);">
                    Verify Your Purchase Code
                </div>
                <p style="font-size:12px; color:var(--muted); margin-bottom:12px;">
                    Before clicking the admin link or homepage, verify the script is licensed by opening
                    the license page in a new tab and confirming your purchase code.
                </p>
                <a href="{{ config('app.url') }}/admin/license" target="_blank" rel="noopener"
                    class="btn btn-primary" style="font-size:14px; padding:10px 24px;">
                    Verify License →
                </a>
            </div>

            {{-- Action buttons --}}
            <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('installer.complete') }}" class="btn btn-primary" style="font-size:15px; padding:12px 32px;">
                    Go to Admin Panel
                </a>
                <a href="{{ route('installer.complete', ['redirect' => 'home']) }}" class="btn btn-outline"
                    style="font-size:15px; padding:12px 32px;">
                    Go to Homepage
                </a>
            </div>

        </div>
@endsection