@extends('installer.layout')
@php $currentStep = 0; @endphp
@section('title', 'Welcome')

@section('content')
    <div style="text-align:center; padding: 20px 0;">
    
        <h1 class="card-title" style="font-size:28px; margin-bottom:10px;">Welcome to
            {{ config('app.name', 'Distro Supawave') }}</h1>
        <p class="card-subtitle" style="font-size:15px; max-width:480px; margin:0 auto 32px;">
            The music distribution platform installer will guide you through setting up your application in just a few
            steps.
        </p>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:36px; text-align:left;">
            <div style="background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:18px;">
                <div style="font-weight:600; font-size:14px; margin-bottom:6px;">Quick Setup</div>
                <div style="font-size:13px; color:var(--muted);">Get up and running in under 5 minutes</div>
            </div>
            <div style="background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:18px;">

                <div style="font-weight:600; font-size:14px; margin-bottom:6px;">Secure</div>
                <div style="font-size:13px; color:var(--muted);">Automatically generates encryption keys</div>
            </div>
            <div style="background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:18px;">
                <div style="font-weight:600; font-size:14px; margin-bottom:6px;">Auto Migration</div>
                <div style="font-size:13px; color:var(--muted);">Creates all database tables automatically</div>
            </div>
        </div>

        <div class="alert alert-warning" style="text-align:left; max-width:480px; margin:0 auto 28px;">
            <div>
                <strong>Before you begin, make sure you have:</strong><br>
                <ul style="margin-top:8px; padding-left:16px; line-height:1.8;">
                    <li>A MySQL database created and ready</li>
                    <li>Your database host, name, username & password</li>
                    <li>PHP 8.1 or higher on your server</li>
                    <li>Write permissions on <code>storage/</code> and <code>bootstrap/cache/</code></li>
                </ul>
            </div>
        </div>

        <a href="{{ route('installer.requirements') }}" class="btn btn-primary" style="font-size:15px; padding:13px 36px;">
            Start Installation →
        </a>
    </div>
@endsection