<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="{{ $global['meta_description'] ?? '' }}">
<title>@yield('title', $global['site_name'])</title>
<link rel="icon" href="{{ asset($global['favicon'] ?? '') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'], 'temp')
@stack('styles')
<style>
:root{
  --bg:    #08080f;
  --bg1:   #0d0d1a;
  --line:  #1c1c2e;
  --t0:    #eeeeff;
  --t1:    #9090b0;
  --t2:    #505070;
  --accent:#7c3aed;
  --fn:    -apple-system,BlinkMacSystemFont,"Helvetica Neue",Helvetica,Arial,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--fn);background:var(--bg);color:var(--t0);-webkit-font-smoothing:antialiased;}
a{text-decoration:none;color:inherit;}

/* NAV */
.nav{
  position:fixed;top:0;left:0;right:0;z-index:200;
  height:64px;
  display:flex;align-items:center;
  padding:0 48px;
  transition:background .25s,border-color .25s;
  border-bottom:1px solid transparent;
}
.nav.solid{
  background:rgba(8,8,15,.96);
  border-color:var(--line);
  backdrop-filter:blur(16px);
}
.nav-logo{
  font-size:17px;font-weight:900;
  letter-spacing:-.03em;color:var(--t0);
  margin-right:auto;flex-shrink:0;
}
.nav-logo img{height:26px;object-fit:contain;}
.nav-links{
  display:flex;align-items:center;gap:28px;
  list-style:none;margin:0 32px;
}
.nav-links a{
  font-size:13px;font-weight:500;color:var(--t1);
  transition:color .15s;white-space:nowrap;
}
.nav-links a:hover{color:var(--t0);}
.nav-login{
  font-size:13px;font-weight:500;
  color:var(--t1);transition:color .15s;
  margin-right:12px;
}
.nav-login:hover{color:var(--t0);}
.nav-cta{
  font-size:13px;font-weight:700;
  padding:8px 18px;border-radius:7px;
  background:var(--accent);color:#fff;
  transition:opacity .15s;flex-shrink:0;
}
.nav-cta:hover{opacity:.85;}
.nav-hamburger{
  display:none;background:none;border:none;
  cursor:pointer;padding:4px;color:var(--t0);
}
.mobile-menu{
  display:none;position:absolute;
  top:64px;left:0;right:0;
  background:rgba(8,8,15,.98);
  border-bottom:1px solid var(--line);
  padding:16px 24px;flex-direction:column;
  gap:2px;
}
.mobile-menu.open{display:flex;}
.mobile-menu a{
  font-size:15px;font-weight:500;color:var(--t1);
  padding:11px 0;border-bottom:1px solid var(--line);
  transition:color .15s;
}
.mobile-menu a:last-child{border-bottom:none;}
.mobile-menu a:hover{color:var(--t0);}

/* FOOTER */
footer{
  background:var(--bg);
  border-top:1px solid var(--line);
  padding:64px 80px 40px;
}
.footer-top{
  display:grid;grid-template-columns:2fr 1fr 1fr 1fr;
  gap:48px;margin-bottom:48px;
}
.footer-brand-name{
  font-size:16px;font-weight:900;
  letter-spacing:-.02em;color:var(--t0);
  margin-bottom:12px;
}
.footer-brand-name img{height:24px;object-fit:contain;}
.footer-tagline{font-size:13px;color:var(--t2);line-height:1.7;max-width:240px;}
.footer-social{display:flex;gap:14px;margin-top:18px;}
.footer-social a{color:var(--t2);transition:color .15s;}
.footer-social a:hover{color:var(--t0);}
.footer-social svg{width:17px;height:17px;fill:currentColor;}
.footer-col h5{
  font-size:11px;font-weight:700;
  letter-spacing:.1em;text-transform:uppercase;
  color:var(--t2);margin-bottom:14px;
}
.footer-col ul{list-style:none;}
.footer-col ul li{margin-bottom:9px;}
.footer-col ul li a{font-size:13px;color:var(--t2);transition:color .15s;}
.footer-col ul li a:hover{color:var(--t0);}
.footer-bottom{
  border-top:1px solid var(--line);padding-top:22px;
  display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;
}
.footer-bottom span{font-size:12px;color:var(--t2);}

@media(max-width:960px){
  .nav{padding:0 24px;}
  .nav-links,.nav-login{display:none;}
  .nav-hamburger{display:block;}
  footer{padding:48px 32px 32px;}
  .footer-top{grid-template-columns:1fr 1fr;gap:32px;}
}
@media(max-width:540px){
  .footer-top{grid-template-columns:1fr;}
}
</style>
</head>
<body>

{{-- NAV --}}
<nav class="nav" id="nav">
  <a href="{{ route('home') }}" class="nav-logo">
    @if(!empty($global['site_logo']))
      <img src="{{ asset($global['site_logo']) }}" alt="{{ $global['site_name'] }}">
    @else
      {{ $global['site_name'] }}
    @endif
  </a>

  <ul class="nav-links">
    <li><a href="{{ route('home') }}#features">Features</a></li>
    <li><a href="{{ route('home') }}#how">How it works</a></li>
    <li><a href="{{ route('home') }}#pricing">Pricing</a></li>
    <li><a href="{{ route('home') }}#contact">Contact</a></li>
  </ul>

  <a href="{{ route('login') }}" class="nav-login">Log in</a>
  <a href="{{ route('register') }}" class="nav-cta">Get started</a>

  <button class="nav-hamburger" id="navHam" aria-label="Open menu">
    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
  </button>
</nav>

<div class="mobile-menu" id="mobileNav">
  <a href="{{ route('home') }}#features">Features</a>
  <a href="{{ route('home') }}#how">How it works</a>
  <a href="{{ route('home') }}#pricing">Pricing</a>
  <a href="{{ route('home') }}#contact">Contact</a>
  <a href="{{ route('login') }}">Log in</a>
  <a href="{{ route('register') }}" style="color:var(--t0);font-weight:700;">Get started →</a>
</div>

{{-- PAGE --}}
@yield('content')

{{-- FOOTER --}}
<footer id="contact">
  <div class="footer-top">
    <div>
      <div class="footer-brand-name">
        @if(!empty($global['site_logo']))
          <img src="{{ asset($global['site_logo']) }}" alt="{{ $global['site_name'] }}">
        @else
          {{ $global['site_name'] }}
        @endif
      </div>
      <p class="footer-tagline">{{ $global['site_tagline'] ?? 'Distribute your music to 150+ platforms worldwide.' }}</p>
      <div class="footer-social">
        @if(!empty($global['instagram']))
        <a href="{{ $global['instagram'] }}" target="_blank" aria-label="Instagram">
          <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
        </a>
        @endif
        @if(!empty($global['twitter']))
        <a href="{{ $global['twitter'] }}" target="_blank" aria-label="X">
          <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>
        @endif
        @if(!empty($global['facebook']))
        <a href="{{ $global['facebook'] }}" target="_blank" aria-label="Facebook">
          <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </a>
        @endif
      </div>
    </div>

    <div class="footer-col">
      <h5>Platform</h5>
      <ul>
        <li><a href="{{ route('home') }}#features">Features</a></li>
        <li><a href="{{ route('home') }}#pricing">Pricing</a></li>
        <li><a href="{{ route('home') }}#how">How it works</a></li>
        <li><a href="{{ route('register') }}">Get started</a></li>
        <li><a href="{{ route('login') }}">Log in</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h5>Legal</h5>
      <ul>
        <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
        <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
        <li><a href="{{ route('cookies.page') }}">Cookie Policy</a></li>
        <li><a href="{{ route('refund.page') }}">Refund Policy</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h5>Contact</h5>
      <ul>
        @if(!empty($global['contact_email']))
        <li><a href="mailto:{{ $global['contact_email'] }}">{{ $global['contact_email'] }}</a></li>
        @endif
        @if(!empty($global['contact_phone']))
        <li><a href="tel:{{ $global['contact_phone'] }}">{{ $global['contact_phone'] }}</a></li>
        @endif
        @if(!empty($global['contact_address']))
        <li><a>{{ $global['contact_address'] }}</a></li>
        @endif
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <span>&copy; {{ date('Y') }} {{ $global['site_name'] }}. All rights reserved.</span>
    <span>Empowering independent artists worldwide.</span>
  </div>
</footer>

<script>
  const nav = document.getElementById('nav');
  const ham = document.getElementById('navHam');
  const mob = document.getElementById('mobileNav');

  window.addEventListener('scroll', () => {
    nav.classList.toggle('solid', window.scrollY > 30);
  });

  ham.addEventListener('click', () => {
    mob.classList.toggle('open');
    nav.classList.add('solid');
  });

  // Close mobile menu on link click
  mob.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => mob.classList.remove('open'));
  });
</script>
@stack('scripts')
</body>
</html>