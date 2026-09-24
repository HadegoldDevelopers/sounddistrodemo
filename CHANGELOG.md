# Changelog

All notable changes to **Distroflow** are documented in this file.

## v1.3.0 — 2026-09-22

### Payments
- Every transaction now records `original_amount` / `original_currency`
  (required by the `transactions` table) for Manual, CoinPayments, MoneyUnify
  and NOWPayments.
- Added a `plan()` relationship to `Transaction` (alias of `subscription_plan()`)
  so all payment callbacks can reliably resolve and activate the purchased plan.
- **NOWPayments**: a pending transaction is now created before redirecting the
  buyer, the `order_id` is a unique UUID (no more `plan_<name>` collisions),
  and the IPN is sent to the POST webhook route (`nowpayments.webhook`).
- **CoinPayments**: the callback now verifies the configured **merchant id**
  in addition to the HMAC signature, and an **IPN Secret** field was added to
  the payment settings UI so admins can configure it.
- **Manual Payment**: the transaction is created once via POST in `process()`
  (server-side amount/currency from the plan) and the instructions page only
  reads the stored record — refreshing never creates duplicates and client
  values are never trusted.
- **MoneyUnify check**: added null guards and an idempotency check so a paid
  payment is never activated twice.

### Royalties
- `massApprove()` is now transaction-safe: each earning is atomically flipped
  `pending → approved` with row-locking before the wallet is credited, so
  concurrent requests can never credit the same earning twice.

### Currency
- Seeded **USD** `conversion_rate` corrected to `1.00` (was 1.35, which would
  have overcharged USD customers by 35%).
- Seeded **MGA** `conversion_rate` corrected from `0.00` to a valid rate.
- Added guards so zero/missing conversion rates can never cause division-by-zero
  or zeroed prices (`convertCurrency` and payment amount calculation).

## v1.2.0 — 2026-09-20

### Security
- Installer self-destruct (`/install/complete`) is now **POST-only** and
  requires a completed installation + one-time session flag. No longer
  triggerable via an unprotected GET request.
- **CoinPayments IPN** now verifies the HMAC-SHA512 signature with the IPN
  secret and resolves the buyer/plan from the stored transaction instead of
  trusting values sent in the callback.
- **CoinPayments checkout** now redirects to the real checkout URL extracted
  from the API response (previously the raw response array was passed to
  `redirect()`).
- **Master audio files** are now stored in **private storage** (`storage/app/private/songs`)
  with unique UUID filenames and served only through authorized endpoints.
  Previously they lived in `public/songs`.
- Track/cover filenames are no longer derived from the project title — UUIDs
  and owner-prefixed chunk names prevent cross-user collision/overwrite.
- Release finalization now **verifies each `audio_path` and `cover_path` was
  uploaded by the current user in the current session** instead of trusting
  client-supplied file paths.
- The default Laravel middleware stack is preserved (no `$middleware->use()`
  replacement).
- Installer now requires **PHP >= 8.2**, matching `composer.json`.

### Payments
- Registered **MoneyUnify** in the payment gateway seeder.
- Fixed the MoneyUnify wait page to poll `payment.moneyunify.check`.
- Completed the **Manual Payment** flow end-to-end: pending transaction is
  created at checkout, and admins can review, approve (activating the
  subscription) or reject it.

## v1.1.0 — 2026-09-18

### Removed
- All purchase-code / license verification (no license server, no phone-home,
  no domain-locking, no lockdown). Distroflow is fully self-contained and
  installs in a clean 4-step installer: Requirements → Database → Environment →
  Admin Account.

### Security & hardening
- Chunked audio upload now enforces a strict extension whitelist and verifies
  the assembled file's MIME (`finfo`) before it is stored in the web root.
- Profile image / logo uploads are validated server-side (`image`, `mimes`,
  `max`) and stored with a server-detected extension.
- Cover art enforces the 3000×3000px minimum server-side.
- Release ownership checks prevent IDOR on `/releases/{project}` and stats pages.
- Wallet operations (withdrawals, royalty approvals, refunds) are wrapped in
  transactions with row-locking and are idempotent against double-processing.
- Payment webhooks (NowPayments, PayPal, CoinPayments) verify signatures and
  guard against duplicate processing.
- Removed leaked error logs; PHP-execution `.htaccess` guards are present in
  every public upload directory.

### Fixes
- All registered routes now resolve to existing controller methods.
- MoneyUnify wait route name corrected.
- Frontend assets are built to `public/temp` (matching the Vite build directory)
  and ship pre-compiled.

## v1.0.0 — 2026-09-16

- Initial release: white-label music distribution platform for artists and labels.