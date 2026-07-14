# NekoKo.rs — Deployment

Local development environment, planned production setup, and the SMTP configuration that's currently blocking all email.

Last verified against the codebase: 2026-07-14.

---

## Development environment

**Local by Flywheel** — all development happens locally on the machine. The site is served at `http://nekoko.local`.

Working directory: `C:\Users\Marko\Local Sites\nekoko\app\public\` (Marko's machine; other machines will differ).

The GitHub repo `basicswithgod-gif/nekoko` contains everything under `wp-content/`. WordPress core, wp-config.php, and the database are NOT in the repo — they're per-environment.

### Branches

- `develop` — active working branch, where all in-progress work lands
- `main` — release branch, currently behind `develop`

Pull requests target `develop`. `main` gets updated at deploy time.

### First-time setup

1. Clone the repo into an existing Local by Flywheel site's `wp-content/` folder
2. Activate the NekoKo Bootstrap plugin — it runs a one-time script that:
   - Activates the required plugin list
   - Sets `nekoko-child` as the active theme (parent theme Hello Elementor must already be installed via Themes → Add New)
   - Sets permalink structure to `/%postname%/`
   - Sets site title, description, timezone
   - Writes WP Mail SMTP configuration
3. Install the SASS toolchain: `cd wp-content/themes/nekoko-child && npm install`
4. Compile CSS: `npm run build:css`

### ACF Pro

ACF Pro is a paid plugin. The zip is provided by the developer via Slack (`advanced-custom-fields-pro-6.8.0.1.zip`). Install path: wp-admin → Plugins → Add New → Upload Plugin → activate.

The bootstrap plugin expects ACF Pro to be present; if it's missing, the ACF-registered blocks (`acf/nekoko-hero`, `acf/nekoko-featured-jobs`, etc.) will silently fail to register.

---

## Production hosting (planned)

**Hostinger** managed WordPress hosting. Not yet provisioned. The domain `nekoko.rs` is registered with Hostinger and points to their nameservers, but the WordPress install hasn't been done.

Production deploy is a launch-time task. It is **not a prerequisite for feature development** — all EPIC 4 work continues locally. Deploy is only needed for:

- Public URL testing
- Real email delivery to real customers
- Final legal page sign-off
- Real provider onboarding

### Production plugin stack

Same list as local, plus:

- **LiteSpeed Cache** — Hostinger-optimized page cache (not needed locally)
- **Wordfence Security** — should be activated on production (installed but inactive locally)

**Deactivate on production:**

- **Query Monitor** — dev-only debugging tool, exposes info that shouldn't be public

### Deploy checklist (draft)

1. Point `nekoko.rs` DNS at Hostinger's WordPress hosting
2. Provision managed WordPress on Hostinger
3. Enable SSL (Let's Encrypt via Hostinger control panel), enforce HTTPS
4. Upload plugins: install and activate each item from the production stack
5. Upload ACF Pro from zip and enter license key
6. Enter Yoast SEO license (if paid tier used) or keep free
7. Sync `wp-content/plugins/nekoko/` and `wp-content/themes/nekoko-child/` from repo (via SFTP, Git deploy, or manual upload)
8. Compile CSS on production: `npm install && npm run build:css` in the child theme folder
9. Import content from local (pages, terms, media) — or recreate manually
10. Set up UpdraftPlus with scheduled daily backups to remote storage (Marko to provide credentials before deploy)
11. Run Wordfence initial scan
12. Configure SMTP with real credentials (see below)
13. Test end-to-end: registration, booking, email delivery, review token

---

## SMTP configuration

WP Mail SMTP is installed and set up in code, but **the password field is blank**. Until a real SMTP password is set, all `wp_mail()` calls fail silently.

### What's already configured

In `wp-content/plugins/nekoko-bootstrap/nekoko-bootstrap.php`, the bootstrap writes these settings:

- `mailer` = SMTP
- `from_email` = `support@nekoko.rs`
- `from_name` = NekoKo
- `smtp_user` = `support@nekoko.rs`
- `smtp_pass` = (empty — needs to be filled in)
- `smtp_host` and `smtp_port` — need to be filled to match the provider

### What blocks email delivery

Line 85 of `nekoko-bootstrap.php` (approximately) sets `smtp_pass` to an empty string. Every downstream email — booking confirmation, review request, admin notification, safety modal acknowledgment — fails at `wp_mail()` and never reaches the user.

### To unblock

1. Choose an SMTP provider (Hostinger's built-in mail, SendGrid, Brevo, Amazon SES, etc.) and provision `support@nekoko.rs`
2. Get the SMTP host, port, username, and password from the provider
3. Enter them in wp-admin → **WP Mail SMTP → Settings**, OR update `nekoko-bootstrap.php` to write the real values on next run
4. Send a test email from WP Mail SMTP → Email Test — verify it arrives
5. Confirm: register a test provider account, submit a test booking, verify the confirmation email arrives

Until this is done, the following features are visually implemented but functionally broken:

- Booking confirmation emails to customer and provider
- The review token flow (see `review-system.md`)
- Any admin notification (new registration pending, new listing pending)
- Password reset emails from WordPress core

---

## Environment-specific behavior

Some settings differ intentionally between local and production:

| Setting | Local | Production |
|---|---|---|
| Site URL | `http://nekoko.local` | `https://nekoko.rs` |
| HTTPS | off | enforced |
| Query Monitor | active | deactivated |
| Wordfence | inactive | active |
| LiteSpeed Cache | not needed | active |
| SMTP password | can be empty (email won't work locally, that's OK) | must be real |
| UpdraftPlus schedule | not needed | daily |
| Debug logging | on | off (or minimal) |

Local `nekoko.local` will not send email even after SMTP is configured on production — because Local by Flywheel's mailhog / MailPit setup routes locally-sent mail to a debug inbox. That's the intended behavior for dev.

---

## Data seeding

The seed script `wp-content/nekoko-seed.php` is currently broken — it references the legacy `service_listing` CPT and `service_category` taxonomy, both of which no longer exist. It needs a rewrite before it can be run again.

Intended usage after rewrite:

```bash
wp eval-file wp-content/nekoko-seed.php
```

Creates test users (providers + customers) and populates a random 1–10 Jobs per category/subcategory so the site isn't empty during dev.

Do not run the current version — it will silently produce 0 output.
