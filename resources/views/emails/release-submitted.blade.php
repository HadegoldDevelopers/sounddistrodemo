@extends('emails.layout')

@section('title', 'Release Submitted')
@section('banner_color', '#6366f1')
@section('banner_title', 'Release Submitted')
@section('banner_subtitle', $isAdmin ? 'A new release is awaiting your review' : 'We have received your submission')

@section('content')

    @if ($isAdmin)
        <p style="margin:0 0 10px 0; font-size:16px; font-weight:600; color:#111111;">
            New Release Notification
        </p>
    @else
        <p style="margin:0 0 10px 0; font-size:16px; font-weight:600; color:#111111;">
            Hi {{ $project->user->name }},
        </p>
    @endif

    {{-- Release Details --}}
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
                <span style="color:#1e293b; font-weight:600;">{{ $project->user->artist->name ?? $project->user->name ?? 'Unknown Artist' }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Title</span>
                <span style="color:#1e293b; font-weight:600;">{{ $project->title }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Type</span>
                <span style="color:#1e293b; font-weight:600;">{{ $project->type }}</span>
            </td>
        </tr>
        @if ($project->release_date)
        <tr>
            <td style="padding:11px 20px; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Release Date</span>
                <span style="color:#1e293b; font-weight:600;">{{ optional($project->release_date)->format('F j, Y') }}</span>
            </td>
        </tr>
        @endif
    </table>

    {{-- Tracks --}}
    @if ($project->tracks->count())
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

    {{-- Bottom message --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="background-color:#f5f3ff; border-left:4px solid #6366f1; border-radius:6px; padding:14px 16px; font-size:13px; color:#4338ca; line-height:1.6;">
                @if ($isAdmin)
                    <strong style="display:block; margin-bottom:4px;">Action Required</strong>
                    Please review this release in the admin panel.
                @else
                    <strong style="display:block; margin-bottom:4px;">What happens next?</strong>
                    Your release has been received and is pending approval. Our team will
                    review it and you will hear back within <strong>2–3 business days</strong>.
                @endif
            </td>
        </tr>
    </table>
    
    {{-- Sign off --}}
    <p style="margin:0; font-size:14px; color:#555555; line-height:1.7;">
        Thank you for choosing <strong>{{ $global['site_name'] }}</strong>.
    </p>

@endsection