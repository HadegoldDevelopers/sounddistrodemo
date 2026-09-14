@extends('emails.layout')

@section('title', 'Release Approved')
@section('banner_color', '#16a34a')
@section('banner_title', 'Release Approved!')
@section('banner_subtitle', 'Your music is on its way to stores')

@section('content')

    {{-- Greeting --}}
    <p style="margin:0 0 10px 0; font-size:16px; font-weight:600; color:#111111;">
        Hi {{ $project->user->name }},
    </p>

    {{-- Intro --}}
    <p style="margin:0 0 24px 0; font-size:14px; color:#555555; line-height:1.7;">
        Great news! Your release has been reviewed and approved by our team.
        It will now be delivered to all major music stores and streaming platforms.
        This process typically takes <strong>5–7 business days</strong>.
    </p>

    {{-- Release details --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:24px; overflow:hidden;">
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
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Type</span>
                <span style="color:#1e293b; font-weight:600;">{{ $project->type }}</span>
            </td>
        </tr>
        @if ($project->genre)
        <tr>
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Genre</span>
                <span style="color:#1e293b; font-weight:600;">{{ $project->genre }}</span>
            </td>
        </tr>
        @endif
        @if ($project->release_date)
        <tr>
            <td style="padding:11px 20px; border-bottom:1px solid #f1f5f9; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">Release Date</span>
                <span style="color:#1e293b; font-weight:600;">{{ \Carbon\Carbon::parse($project->release_date)->format('F j, Y') }}</span>
            </td>
        </tr>
        @endif
        @if ($project->upc)
        <tr>
            <td style="padding:11px 20px; font-size:13px;">
                <span style="display:inline-block; width:130px; color:#94a3b8; font-weight:500;">UPC</span>
                <span style="color:#1e293b; font-weight:600;">{{ $project->upc }}</span>
            </td>
        </tr>
        @endif
    </table>

    {{-- Tracks --}}
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
                @if ($track->isrc)
                    <span style="float:right; font-size:11px; color:#94a3b8;">ISRC: {{ $track->isrc }}</span>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    @endif
    
    {{-- Notice --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
    <tr>
        <td style="background-color:#f5f3ff; border-left:4px solid #6366f1; border-radius:6px; padding:14px 16px; font-size:13px; color:#4338ca; line-height:1.6;">
            <strong style="display:block; margin-bottom:6px;">What happens next?</strong>

            Our team will now submit your release to all supported music stores and streaming platforms.
            Store processing typically takes <strong>5–7 business days</strong>.

            <br><br>

            Once your release goes live, you may receive another email containing your
            <strong>store links</strong> so you can start sharing your music with fans.

            <br><br>

            You can also track your release status anytime from your dashboard.
        </td>
    </tr>
</table>
  

    {{-- Sign off --}}
    <p style="margin:0; font-size:14px; color:#555555; line-height:1.7;">
        Thank you for choosing <strong>{{ $global['site_name'] }}</strong>.
    </p>

@endsection



