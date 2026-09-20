@extends('installer.layout')
@php $currentStep = 2; @endphp
@section('title', 'Database Setup')

@section('content')
    <h2 class="card-title">Database Configuration</h2>
    <p class="card-subtitle">Enter your MySQL database credentials. The database must already exist.</p>

    <form method="POST" action="{{ route('installer.database.save') }}" id="dbForm">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label>Database Host <span class="req">*</span></label>
                <input type="text" name="db_host" value="{{ old('db_host', 'localhost') }}" placeholder="localhost" required
                    class="{{ $errors->has('db_host') ? 'error-field' : '' }}">
                @error('db_host')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Database Port <span class="req">*</span></label>
                <input type="number" name="db_port" value="{{ old('db_port', '3306') }}" placeholder="3306" required>
            </div>
        </div>

        <div class="form-group">
            <label>Database Name <span class="req">*</span></label>
            <input type="text" name="db_name" value="{{ old('db_name') }}" placeholder="distroflow_db" required
                class="{{ $errors->has('db_name') ? 'error-field' : '' }}">
            <div class="field-hint">The database must already be created on your server.</div>
            @error('db_name')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Database Username <span class="req">*</span></label>
                <input type="text" name="db_username" value="{{ old('db_username') }}" placeholder="root" required
                    class="{{ $errors->has('db_username') ? 'error-field' : '' }}">
                @error('db_username')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Database Password</label>
                <input type="password" name="db_password" placeholder="Leave blank if none">
                <div class="field-hint">Leave empty if your database has no password.</div>
            </div>
        </div>

        @error('db_connection')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        {{-- Test Connection --}}
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
            <button type="button" id="testBtn" class="btn btn-outline btn-sm" onclick="testConnection()">
            Test Connection
            </button>
            <span id="conn-status"></span>
        </div>

        <div class="btn-row">
            <a href="{{ route('installer.requirements') }}" class="btn btn-outline">← Back</a>
            <button type="submit" class="btn btn-primary">Save & Continue →</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        function testConnection() {
            const btn = document.getElementById('testBtn');
            const status = document.getElementById('conn-status');

            const data = {
                db_host: document.querySelector('[name=db_host]').value,
                db_port: document.querySelector('[name=db_port]').value,
                db_name: document.querySelector('[name=db_name]').value,
                db_username: document.querySelector('[name=db_username]').value,
                db_password: document.querySelector('[name=db_password]').value,
                _token: document.querySelector('[name=_token]').value,
            };

            btn.innerHTML = '<div class="spinner"></div> Testing...';
            btn.disabled = true;
            status.textContent = '';

            fetch('{{ route("installer.database.test") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': data._token },
                body: JSON.stringify(data),
            })
                .then(r => r.json().catch(() => ({ success: false, message: 'Server error (' + r.status + '). Check the logs.' })))
                .then(res => {
                    if (res.success) {
                        status.innerHTML = '<span style="color:#86efac;">✓ ' + res.message + '</span>';
                    } else {
                        status.innerHTML = '<span style="color:#fca5a5;">✗ ' + res.message + '</span>';
                    }
                })
                .catch(() => {
                    status.innerHTML = '<span style="color:#fca5a5;">✗ Request failed</span>';
                })
                .finally(() => {
                    btn.innerHTML = 'Test Connection';
                    btn.disabled = false;
                });
        }
    </script>
@endpush