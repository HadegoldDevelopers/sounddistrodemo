@extends('installer.layout')
@php $currentStep = 5; @endphp
@section('title', 'Admin Account')

@section('content')
    <h2 class="card-title">Create Admin Account</h2>
    <p class="card-subtitle">This is the final step. We'll run the database migrations and create your admin account.</p>

    <form method="POST" action="{{ route('installer.admin.save') }}" id="installForm" onsubmit="return startInstall()">
        @csrf

        <div class="form-group">
            <label>Admin FullName <span class="req">*</span></label>
            <input type="text" name="admin_name" value="{{ old('admin_name') }}" placeholder="John Doe" required autofocus>
        </div>

        <div class="form-group">
            <label>Admin Email Address <span class="req">*</span></label>
            <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="admin@yourdomain.com"
                required>
            <div class="field-hint">You'll use this to log in to the admin panel.</div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Password <span class="req">*</span></label>
                <input type="password" name="admin_password" id="password" placeholder="Min. 8 characters" required
                    minlength="8">
            </div>
            <div class="form-group">
                <label>Confirm Password <span class="req">*</span></label>
                <input type="password" name="admin_password_confirmation" placeholder="Repeat password" required>
            </div>
        </div>

        @error('install')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        {{-- What happens next --}}
        <div
            style="background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:18px; margin-bottom:8px;">
            <div style="font-size:13px; font-weight:600; margin-bottom:12px; color:var(--text);">
                What happens when you click "Install":
            </div>
            <ul style="list-style:none; display:flex; flex-direction:column; gap:8px;">
                @foreach([
                            'Write .env configuration file',
                            'Run all database migrations',
                            'Seed initial data',
                            'Create your admin account',
                            'Generate app encryption key',
                            'Create storage symlink',
                        ] as $step)
                            <li style="font-size:13px; color:var(--muted); display:flex; align-items:center; gap:8px;">
                                {{ $step }}
                    </li>
                @endforeach
                    </ul>
                </div>

     <div class="btn-row">
         <a href="{{ route('installer.license') }}" class="btn btn-outline">← Back</a>

         <button type="submit" id="installBtn" class="btn btn-primary">
    Install Now
        </button>
                </div>
           </form>
   @endsection
    
       @push('scripts')
                <script>
            function startInstall() {
            const pass = document.getElementById('password').value;
                const conf = document.querySelector('[name=admin_password_confirmation]').value;

                if (pass !==conf) {
                alert('Passwords do not match!');
                    return false;
                }

            const btn = document.getElementById('installBtn');
            btn.innerHTML = '<div class="spinner"></div> Installing...';
            btn.disabled  = true;

            return true;
        }
        </script>
    @endpush