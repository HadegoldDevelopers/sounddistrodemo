# Distro Supawave — White-Label Music Distribution Platform

Distro Supawave is a white-label music distribution platform built with **Laravel 12**. Artists and labels submit releases, admins review and approve them, download metadata packages, and push to any distributor (CDBaby, Symphonic, DistroKid, etc.). Royalties are imported via CSV and paid out through an earnings approval workflow.

## Features

- **User roles** — Admin, Artist, and Label with separate dashboards
- **Release upload** — Single / EP / Album, 3000×3000px cover validation, chunked audio upload (large WAV/MP3 files work on shared hosting)
- **Admin release management** — approve / reject, metadata editing, and a ZIP export (audio + cover + JSON/CSV/PDF metadata)
- **Copyright scanner (ACRCloud)** — every upload is fingerprinted automatically; matches are flagged in the admin release queue (free tier: 100 identifications/month)
- **Royalties & CSV import** — Soundrop + auto-detect parsers with duplicate protection
- **Earnings & withdrawals** — pending/approve workflow, wallet balances, manual payout methods
- **Streaming stats** — store-by-store breakdowns, country filters, monthly trends, manual admin entry
- **Payment gateways** — PayPal, Paystack, CoinPayments, NowPayments, MoneyUnify, and Manual
- **Homepage CMS** — edit the entire landing page from the admin panel (hero, features, artists, CTA)
- **License enforcement** — CodeCanyon purchase-code verification with 24h re-checks and escalation (1–2 failures blocks admin, 3+ full lockdown)
- **6-step web installer** — requirements check, database setup, environment, license, admin account

## Requirements

- PHP **8.2+** with common extensions (BCMath, cURL, DOM, Fileinfo, Mbstring, OpenSSL, PDO, XML, ZipArchive)
- MySQL **5.7+** / MariaDB **10.3+**
- Apache or Nginx
- Composer **2.0+**

## Installation

The package includes the `vendor/` folder, so no Composer/SSH is required — works on any cPanel/shared host.

```bash
# 1. Upload the package contents to your domain (public_html or the domain root)
# 2. Copy the env template (or leave it — the web installer creates it automatically)
cp .env.example .env
```

Then visit your domain — you'll be redirected to the web installer at `/install`. The installer detects a missing `.env`, clones `.env.example`, and handles requirements, database, environment, license, and admin setup for you.

> Optional: if you have SSH, you can instead run `composer install --optimize-autoloader --no-dev` to rebuild `vendor/`.

## Configuration

Create your `.env` from `.env.example` and set your database credentials, SMTP mail, and payment keys. Full configuration details are in [the documentation](documentation/index.html).

## Tech Stack

- Laravel 12, PHP 8.2+
- MySQL / MariaDB
- Tailwind CSS (via Vite)
- Alpine.js
- ACRCloud audio recognition API

## License

Proprietary — commercial product. Do not redistribute without a valid license.
