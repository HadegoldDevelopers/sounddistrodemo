<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', $global['site_name'])</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f7; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; -webkit-font-smoothing:antialiased;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f7; padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="580" cellpadding="0" cellspacing="0" border="0" style="max-width:580px; width:100%;">

                    {{-- ── Logo / Site name ─────────────────────── --}}
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            @if (!empty($global['site_logo']))
                                <img src="{{ asset($global['site_logo']) }}"
                                     alt="{{ $global['site_name'] }}"
                                     style="max-height:48px; width:auto; display:block; margin:0 auto;">
                            @else
                                <span style="font-size:20px; font-weight:700; color:#111111;">
                                    {{ $global['site_name'] }}
                                </span>
                            @endif
                        </td>
                    </tr>

                    {{-- ── Card ─────────────────────────────────── --}}
                    <tr>
                        <td style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.06);">

                            {{-- Banner --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center"
                                        style="background-color:@yield('banner_color', '#6366f1'); padding:36px 40px; border-radius:12px 12px 0 0;">
                                        <h1 style="margin:0 0 6px 0; color:#ffffff; font-size:22px; font-weight:700;">
                                            @yield('banner_title')
                                        </h1>
                                        <p style="margin:0; color:rgba(255,255,255,0.85); font-size:14px;">
                                            @yield('banner_subtitle')
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Body --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding:36px 40px;">
                                        @yield('content')
                                    </td>
                                </tr>
                            </table>

                            {{-- Footer --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center"
                                        style="padding:20px 40px; border-top:1px solid #f1f5f9; font-size:12px; color:#aaaaaa; line-height:1.7;">
                                        &copy; {{ date('Y') }} {{ $global['site_name'] }}. All rights reserved.<br>
                                        This email was sent to {{ $recipientEmail ?? '' }}
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>