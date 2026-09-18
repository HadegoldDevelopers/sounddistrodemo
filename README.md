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
- **4-step web installer** — requirements check, database setup, environment, and admin account

## Requirements

- PHP **8.2+** with common extensions (BCMath, cURL, DOM, Fileinfo, Mbstring, OpenSSL, PDO, XML, ZipArchive)
- MySQL **5.7+** / MariaDB **10.3+**
- Apache or Nginx
- Composer **2.0+**

## Installation (cPanel / Shared Hosting)

The package is **fully pre-installable** — it ships with the compiled `vendor/` directory and pre-built frontend assets (`public/temp`), so **no Composer, SSH, or Node.js is required**. It runs on any cPanel/shared host with PHP 8.2+.

### 1. Upload the files
1. Unzip the package on your computer. You'll get a `sounddistro/` folder.
2. In cPanel → **File Manager**, navigate to your web root:
   - **Main domain** → `public_html/`
   - **Addon/subdomain** → the subdomain's document root (e.g. `subdomain/`)
3. Upload the **contents** of the `sounddistro/` folder (not the folder itself) into that directory, **or** upload the whole folder so your app lives at e.g. `public_html/sounddistro/`.

### 2. Create the database
1. In cPanel → **MySQL® Databases**, create a database and a database user.
2. Assign the user to the database with **ALL PRIVILEGES**.
3. Keep the database name, username, and password ready.

### 3. Set permissions
Make these folders writable (set to `755`/`775` as your host allows):
- `storage/` and everything inside it
- `bootstrap/cache/`
- `public/songs`, `public/covers`, `public/profile_images`, `public/temp`

### 4. Run the web installer
Visit your domain. You'll be redirected to the **web installer** at `/install` (e.g. `https://yourdomain.com/install`). It walks you through:
1. **Requirements** — PHP version & extensions check
2. **Database** — enter the credentials from step 2
3. **Environment** — app name, URL, mail settings
4. **Admin account** — create your admin login

The installer creates the `.env` file, runs the database migrations/seeds, and sets everything up automatically.

> If you are **not** redirected to the installer, it may already be configured. Delete the `storage/installed` file (if present) and refresh.

### Alternative: manual setup (with SSH)
If you have shell access you can set things up manually instead:

```bash
cp .env.example .env
php artisan key:generate
# edit .env with your DB credentials, then:
php artisan migrate --seed
php artisan storage:link
```

> Optional rebuilds — only needed if you changed source: `composer install --optimize-autoloader --no-dev` (vendor) or `npm install && npm run build` (frontend).

## What's included in the package

- Full Laravel 12 application source (`app/`, `config/`, `routes/`, `resources/`, `database/`, `public/`)
- Pre-compiled **`vendor/`** directory — no Composer needed
- Pre-built frontend assets in **`public/temp/`** — no Node/npm needed
- Root **`.htaccess`** for cPanel/LiteSpeed routing + `public/.htaccess`
- **`documentation/`** — full HTML user/admin documentation
- **web installer** for one-click setup
- `.env.example`, `composer.json`/`composer.lock`, `package.json`/`package-lock.json`, `artisan`

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
