<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>License Verification</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f4f4f7; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { width: 100%; max-width: 460px; background: #fff; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,.12); padding: 32px; }
        .logo { text-align: center; font-weight: 800; font-size: 20px; letter-spacing: .5px; margin-bottom: 6px; }
        h1 { font-size: 20px; margin: 0 0 8px; }
        p  { font-size: 14px; color: #6b7280; line-height: 1.55; }
        .msg { padding: 12px; border-radius: 8px; margin: 14px 0; font-size: 14px; }
        .msg.ok { background: #ecfdf5; color: #15803d; }
        .msg.err { background: #fef2f2; color: #b91c1c; }
        label { display: block; font-size: 13px; font-weight: 600; margin: 0 0 6px; }
        input { width: 100%; padding: 11px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
        button { width: 100%; margin-top: 16px; padding: 12px; border: 0; border-radius: 8px; background: #A020F0; color: #fff; font-size: 15px; font-weight: 600; cursor: pointer; }
        .back { display: block; text-align: center; margin-top: 14px; font-size: 13px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">{{ $global['site_name'] }}</div>
        <h1>License Verification</h1>
        <p>Enter your CodeCanyon purchase code to restore access to this installation.</p>

        @if(session('error'))
            <div class="msg err">{{ session('error') }}</div>
        @elseif(session('success'))
            <div class="msg ok">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.license.update') }}">
            @csrf
            <label for="purchase_code">Purchase Code</label>
            <input type="text" name="purchase_code" id="purchase_code" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                   value="{{ old('purchase_code') }}" required autofocus>
            @error('purchase_code')<p style="color:#b91c1c;font-size:13px;margin-top:6px;">{{ $message }}</p>@enderror
            <button type="submit">Verify License</button>
        </form>

        <a class="back" href="{{ route('admin.login') }}">Back to login</a>
    </div>
</body>
</html>