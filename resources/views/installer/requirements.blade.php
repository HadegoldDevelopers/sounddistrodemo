@extends('installer.layout')
@php $currentStep = 1; @endphp
@section('title', 'Server Requirements')

@section('content')
    <h2 class="card-title">Server Requirements</h2>
    <p class="card-subtitle">Checking that your server meets all requirements to run DistroKit.</p>

    {{-- PHP Extensions --}}
    <div class="section-title">PHP Extensions</div>
    <table class="req-table">
        <thead>
            <tr>
                <th>Requirement</th>
                <th>Required</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requirements as $req)
                <tr>
                    <td>{{ $req['label'] }}</td>
                    <td style="color:var(--muted); font-size:12px;">{{ $req['value'] }}</td>
                    <td>
                        @if($req['status'])
                            <span class="badge badge-success">✓ Pass</span>
                        @else
                            <span class="badge badge-danger">✗ Fail</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Folder Permissions --}}
    <div class="section-title" style="margin-top:28px;">Folder Permissions</div>
    <table class="req-table">
        <thead>
            <tr>
                <th>Directory</th>
                <th>Required</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $perm)
                <tr>
                    <td><code style="font-size:12px; color:var(--muted);">{{ $perm['label'] }}</code></td>
                    <td style="color:var(--muted); font-size:12px;">{{ $perm['required'] }}</td>
                    <td>
                        @if($perm['status'])
                            <span class="badge badge-success">✓ Writable</span>
                        @else
                            <span class="badge badge-danger">✗ Not Writable</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!$allPermissionsMet)
        <div class="alert alert-warning" style="margin-top:20px;">
            <div>
                <strong>Fix permissions by running:</strong><br>
                <code
                    style="font-size:12px; display:block; margin-top:8px; background:rgba(0,0,0,.3); padding:8px 12px; border-radius:6px;">
                    chmod -R 775 storage bootstrap/cache public/temp<br>
                    chown -R www-data:www-data storage bootstrap/cache
                </code>
            </div>
        </div>
    @endif

    <div class="btn-row">
        <a href="{{ route('installer.welcome') }}" class="btn btn-outline">← Back</a>

        @if($canProceed)
            <a href="{{ route('installer.database') }}" class="btn btn-primary">
                Continue → Database
            </a>
        @else
            <button class="btn btn-primary" disabled style="opacity:.4; cursor:not-allowed;">
                Fix Issues to Continue
            </button>
        @endif
    </div>

    @if(!$canProceed)
        <div style="text-align:center; margin-top:16px;">
            <a href="{{ route('installer.requirements') }}" style="font-size:13px; color:var(--primary);">
                🔄 Re-check requirements
            </a>
        </div>
    @endif
@endsection