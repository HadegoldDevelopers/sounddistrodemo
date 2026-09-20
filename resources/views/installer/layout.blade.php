<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Install') — {{ config('app.name', 'Distroflow') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #6366f1;
            --primary-d: #4f46e5;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg: #0f0f13;
            --surface: #18181f;
            --surface2: #1f1f2e;
            --border: #2a2a3a;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --radius: 12px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .installer-wrap {
            width: 100%;
            max-width: 780px;
        }

        /* Header */
        .installer-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .installer-header .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 20px;
        }

        .installer-header .logo span {
            background: linear-gradient(135deg, #6366f1, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Steps bar */
        .steps-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 32px;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 18px;
            left: calc(50% + 18px);
            width: calc(100% - 8px);
            height: 2px;
            background: var(--border);
            z-index: 0;
        }

        .step-item.done:not(:last-child)::after {
            background: var(--primary);
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            border: 2px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            position: relative;
            z-index: 1;
            transition: all .3s;
        }

        .step-item.done .step-circle {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .step-item.active .step-circle {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, .2);
        }

        .step-label {
            font-size: 11px;
            color: var(--muted);
            margin-top: 6px;
            white-space: nowrap;
            min-width: 80px;
            text-align: center;
        }

        .step-item.active .step-label,
        .step-item.done .step-label {
            color: var(--text);
        }

        /* Card */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 40px;
        }

        .card-title {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .card-subtitle {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 32px;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 6px;
        }

        label span.req {
            color: var(--danger);
            margin-left: 2px;
        }

        input[type=text],
        input[type=email],
        input[type=url],
        input[type=password],
        input[type=number],
        select {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            color: var(--text);
            font-family: inherit;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
        }

        input.error-field {
            border-color: var(--danger);
        }

        .field-hint {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }

        .field-error {
            font-size: 12px;
            color: var(--danger);
            margin-top: 4px;
        }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-danger {
            background: rgba(239, 68, 68, .1);
            border: 1px solid rgba(239, 68, 68, .3);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(34, 197, 94, .1);
            border: 1px solid rgba(34, 197, 94, .3);
            color: #86efac;
        }

        .alert-warning {
            background: rgba(245, 158, 11, .1);
            border: 1px solid rgba(245, 158, 11, .3);
            color: #fcd34d;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-d);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-sm {
            padding: 7px 14px;
            font-size: 12px;
        }

        .btn-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        /* Requirements table */
        .req-table {
            width: 100%;
            border-collapse: collapse;
        }

        .req-table th {
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--muted);
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
        }

        .req-table td {
            padding: 10px 12px;
            font-size: 13px;
            border-bottom: 1px solid rgba(42, 42, 58, .5);
        }

        .req-table tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(34, 197, 94, .15);
            color: #86efac;
        }

        .badge-danger {
            background: rgba(239, 68, 68, .15);
            color: #fca5a5;
        }

        /* Section title inside card */
        .section-title {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--muted);
            margin: 24px 0 12px;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        /* Connection test badge */
        #conn-status {
            font-size: 13px;
            font-weight: 500;
        }

        /* Finish checkmarks */
        .finish-list {
            list-style: none;
        }

        .finish-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        .finish-list li:last-child {
            border-bottom: none;
        }

        .check-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(34, 197, 94, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Loading spinner */
        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, .3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 600px) {
            .card {
                padding: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .step-label {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="installer-wrap">

        {{-- Header --}}
        <div class="installer-header">
            <div class="logo">
                <span>{{ config('app.name', 'Distroflow') }}</span>
            </div>

            {{-- Steps --}}
            @php
                $currentStep = $currentStep ?? 0;
                $steps = [
                    1 => 'Requirements',
                    2 => 'Database',
                    3 => 'Environment',
                    4 => 'Admin Account',
                    5 => 'Finish',
                ];
            @endphp
            <div class="steps-bar" style="gap:40px;">
                @foreach($steps as $num => $label)
                    @php
                        $state = $num < $currentStep ? 'done' : ($num === $currentStep ? 'active' : '');
                    @endphp
                    <div class="step-item {{ $state }}">
                        <div class="step-circle">
                            @if($num < $currentStep)
                                ✓
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <div class="step-label">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Errors --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Page content --}}
        <div class="card">
            @yield('content')
        </div>

    </div>
    @stack('scripts')
</body>

</html>