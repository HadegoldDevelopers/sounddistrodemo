# Changelog

All notable changes to **Distroflow** are documented in this file.

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