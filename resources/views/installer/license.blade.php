@extends('installer.layout')
@php $currentStep = 4; @endphp
@section('title', 'License Verification')

@section('content')
    <h2 class="card-title">Verify Your Purchase</h2>
    <p class="card-subtitle">Enter your CodeCanyon purchase code to activate this installation. The code is verified once against the license server.</p>

    <form method="POST" action="{{ route('installer.license.save') }}">
        @csrf

        <div class="form-group">
            <label>CodeCanyon Purchase Code <span class="req">*</span></label>
            <input type="text" name="purchase_code" value="{{ old('purchase_code') }}"
                placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" required autofocus>
            <div class="field-hint">Found under your CodeCanyon Downloads page for this item.</div>
            @error('purchase_code')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn-primary">Verify &amp; Continue</button>
            <a href="{{ route('installer.database') }}" class="btn btn-outline">← Back</a>
        </div>
    </form>
@endsection