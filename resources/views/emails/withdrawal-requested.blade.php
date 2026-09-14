@extends('emails.layout')
@section('title', 'Withdrawal Request')
@section('banner_color', '#0369a1')
@section('banner_title', $isAdmin ? 'New Withdrawal Request' : 'Withdrawal Request Received')
@section('banner_subtitle', $isAdmin ? 'A payout request requires your attention' : 'We have received your withdrawal request')

@section('content')

    @if ($isAdmin)
        <p style="margin:0 0 10px 0; font-size:16px; font-weight:600; color:#111111;">
            Hi {{ $admin->name }},
        </p>
        <p style="margin:0 0 24px 0; font-size:14px; color:#555555; line-height:1.7;">
            A user has submitted a withdrawal request and is awaiting your approval.
        </p>
    @else
        <p style="margin:0 0 10px 0; font-size:16px; font-weight:600; color:#111111;">
            Hi {{ $withdrawal->user->name }},
        </p>
        <p style="margin:0 0 24px 0; font-size:14px; color:#555555; line-height:1.7;">
            Your withdrawal request has been received and is awaiting approval.
            You will be notified once a decision has been made.
        </p>
    @endif

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:24px;">
        <tr>
            <td style="background-color:#f1f5f9; padding:10px 20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#64748b; border-bottom:1px solid #e2e8f0;">
                Request Details
            </td>
        </tr>
        <tr>
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">User</span>
                <span style="color:#1e293b; font-weight:600;">{{ $withdrawal->user->name }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Email</span>
                <span style="color:#1e293b; font-weight:600;">{{ $withdrawal->user->email }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Amount</span>
                <span style="color:#1e293b; font-weight:600;">${{ number_format($withdrawal->amount, 2) }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Method</span>
                <span style="color:#1e293b; font-weight:600;">{{ ucfirst($withdrawal->method) }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 20px; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Date</span>
                <span style="color:#1e293b; font-weight:600;">{{ $withdrawal->created_at->format('F j, Y') }}</span>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="background-color:#f5f3ff; border-left:4px solid #0369a1; border-radius:6px; padding:14px 16px; font-size:13px; color:#075985; line-height:1.6;">
                @if ($isAdmin)
                    <strong style="display:block; margin-bottom:4px;">Action Required</strong>
                    Please log in to the admin panel to approve or reject this request.
                @else
                    <strong style="display:block; margin-bottom:4px;">What happens next?</strong>
                    Our team will review your request and you will receive an email once approved.
                @endif
            </td>
        </tr>
    </table>

@endsection