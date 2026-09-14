@extends('emails.layout')
@section('title', 'Withdrawal Approved')
@section('banner_color', '#16a34a')
@section('banner_title', 'Withdrawal Approved')
@section('banner_subtitle', 'Your payout has been processed')

@section('content')

    <p style="margin:0 0 10px 0; font-size:16px; font-weight:600; color:#111111;">
        Hi {{ $user->name }},
    </p>

    <p style="margin:0 0 24px 0; font-size:14px; color:#555555; line-height:1.7;">
        Your withdrawal request has been <strong>approved</strong> and the amount has been transferred successfully.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:24px;">
        <tr>
            <td style="background-color:#f1f5f9; padding:10px 20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#64748b; border-bottom:1px solid #e2e8f0;">
                Withdrawal Details
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
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Paid To</span>
                <span style="color:#1e293b; font-weight:600;">
                    @if ($withdrawal->method === 'paypal')
                        {{ $withdrawal->details['paypal_email'] ?? '-' }}
                    @elseif ($withdrawal->method === 'bank')
                        {{ $withdrawal->details['info'] ?? '-' }}
                    @elseif ($withdrawal->method === 'crypto')
                        {{ $withdrawal->details['crypto_wallet'] ?? '-' }}
                    @else
                        -
                    @endif
                </span>
            </td>
        </tr>
    </table>

    <p style="margin:0; font-size:14px; color:#555555; line-height:1.7;">
        Thank you for choosing <strong>{{ $global['site_name'] }}</strong>.
    </p>

@endsection