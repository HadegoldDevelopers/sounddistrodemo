@extends('emails.layout')

@section('title', 'Release Rejected')
@section('banner_color', '#dc2626')
@section('banner_title', 'Release Not Approved')
@section('banner_subtitle', 'Your submission requires attention')

@section('content')

    <p style="margin:0 0 10px 0; font-size:16px; font-weight:600; color:#111111;">
        Hi {{ $project->user->name }},
    </p>

    <p style="margin:0 0 24px 0; font-size:14px; color:#555555; line-height:1.7;">
        Unfortunately your release was not approved at this time.
        Please review the details below and resubmit after making the necessary corrections.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:24px;">
        <tr>
            <td style="background-color:#f1f5f9; padding:10px 20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#64748b; border-bottom:1px solid #e2e8f0;">
                Release Details
            </td>
        </tr>
        <tr>
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Artist</span>
                <span style="color:#1e293b; font-weight:600;">{{ optional($project->user->artist)->name ?? $project->user->name }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Title</span>
                <span style="color:#1e293b; font-weight:600;">{{ $project->title }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 20px; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Type</span>
                <span style="color:#1e293b; font-weight:600;">{{ $project->type }}</span>
            </td>
        </tr>
    </table>

    @if ($project->tracks->isNotEmpty())
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:24px;">
        <tr>
            <td style="background-color:#f1f5f9; padding:10px 20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#64748b; border-bottom:1px solid #e2e8f0;">
                {{ $project->tracks->count() }} {{ Str::plural('Track', $project->tracks->count()) }}
            </td>
        </tr>
        @foreach ($project->tracks as $track)
        <tr>
            <td style="padding:11px 20px; {{ !$loop->last ? 'border-bottom:1px solid #f1f5f9;' : '' }} font-size:13px; color:#1e293b;">
                <strong>{{ $track->track_number }}.</strong> {{ $track->title }}
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
        <tr>
            <td style="background-color:#fef2f2; border-left:4px solid #dc2626; border-radius:6px; padding:14px 16px; font-size:13px; color:#991b1b; line-height:1.6;">
                <strong style="display:block; margin-bottom:4px;">What to do next?</strong>
                Review your submission for any issues with audio quality, cover art, or metadata.
                Once corrected, you may resubmit your release through your dashboard.
            </td>
        </tr>
    </table>

    <p style="margin:0; font-size:14px; color:#555555; line-height:1.7;">
        If you have any questions or believe this decision was made in error, please contact our support team at
        <a href="mailto:{{ $global['email'] }}" style="color:#6366f1;">{{ $global['email'] }}</a>.<br><br>
        Thank you for choosing <strong>{{ $global['site_name'] }}</strong>.
    </p>

@endsection