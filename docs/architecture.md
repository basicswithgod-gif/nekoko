# NekoKo.rs — Architecture

This document describes the current, verified state of the NekoKo.rs codebase. It is authoritative: when Jira, memory notes, and this document disagree, this document (and the code it describes) is correct.

Last verified against the codebase: 2026-07-14.

---

## Overview

NekoKo.rs is a WordPress-based marketplace for niche services in Serbia. The core differentiator is that the platform sells **the person, not the company**: reviews are tied to individual providers, and customers choose *who* will serve them before booking.

The platform does **not** process payments, verify provider qualifications, or mediate disputes. It facilitates discovery only.

---

## Tech stack

| Layer | Choice |
|---|---|
| CMS | WordPress |
| Local dev | Local by Flywheel (`nekoko.local`) |
| Production hosting (planned) | Hostinger |
| Parent theme | Hello Elementor (starter shell only — no Elementor page builder plugin) |
| Child theme | `nekoko-child` (all customization lives here) |
| Custom plugin | `nekoko` (CPTs, blocks, shortcodes, moderation, review + booking systems) |
| Bootstrap plugin | `nekoko-bootstrap` (one-time site setup: activates plugins, sets theme, permalinks, SMTP config) |
| Page building | 100% Gutenberg — mix of native and ACF-registered blocks |
| Custom fields | ACF Pro 6.8.0.1 (installed via provided zip) |
| Styling | SASS, compiled to minified CSS via `sass` npm package |
| Repo | `basicswithgod-gif/nekoko`, `develop` branch is the working branch |

---

## Plugin stack

**Required (installed and active on `nekoko.local`):**

| Plugin | Purpose |
|---|---|
| Advanced Custom Fields PRO 6.8.0.1 | Custom fields on taxonomies + ACF-registered Gutenberg blocks |
| Yoast SEO 27.8 | SEO |
| Loco Translate 2.8.5 | Translations |
| SVG Support 2.5.16 | SVG media upload + inline rendering |
| Simple History 5.29.0 | Admin audit log |
| Query Monitor 4.0.6 | Debugging (dev only — deactivate on production) |
| Imagify 2.2.8 | Image optimization |
| Virtual Robots.txt 1.10 | Dynamic robots.txt |
| WP Mail SMTP 4.8.0 | Transactional email (currently blocked — no SMTP password set) |
| WPForms Lite | Contact form (form ID 130) |
| User Role Editor 4.65 | Role capability management |
| UpdraftPlus 1.26.5 | Scheduled backups |
| NekoKo Bootstrap 1.0.0 | One-time site setup — safe to keep active |
| NekoKo Core 1.0.0 | The custom plugin containing everything below |

**Installed but inactive:** Wordfence Security 8.2.2 (available if needed, not required by current spec)

**Explicitly forbidden — do not install:**
- WooCommerce (all payment/checkout logic is off-platform)
- Elementor (page builder plugin) — Hello Elementor parent theme is fine
- WP Super Cache (superseded by LiteSpeed Cache in production plan)

---

## Themes

Two themes are present:

- `hello-elementor` (parent) — used only as a minimal shell; no styling relied on
- `nekoko-child` (active) — all site customization lives here

### `nekoko-child` structure

```
themes/nekoko-child/
├── style.css                     — child theme declaration (Template: hello-elementor)
├── functions.php                 — enqueue CSS/JS, register hooks
├── package.json                  — SASS build config
├── scss/
│   ├── main.scss                 — entry point, imports all partials
│   ├── _variables.scss           — brand colors, spacing, fonts from style guide
│   ├── _mixins.scss              — reusable SCSS patterns
│   ├── _hero.scss
│   ├── _categories.scss
│   ├── _job-cards.scss
│   ├── _search-filter.scss
│   ├── _dashboard.scss
│   ├── _homepage.scss
│   └── _footer.scss
├── assets/css/
│   ├── style.min.css             — compiled from scss/main.scss
│   └── blocks.css                — block-specific styles
├── inc/
│   └── blocks.php                — interim native Gutenberg blocks (see "Blocks" section)
├── single-job.php                — single Job template
├── archive-job.php               — Jobs archive template
└── footer.php                    — footer with wp_nav_menu('footer-informacije')
```

### SASS build

```bash
cd wp-content/themes/nekoko-child
npm install
npm run build:css        # one-off compile
npm run watch:css        # dev mode
```

Output: `assets/css/style.min.css` (source maps disabled, style compressed).

### Brand style tokens (from `_variables.scss`)

- Primary: `#004682` (nekoko-blue)
- Accent: `#F5B335` (nekoko-yellow)
- Accent red: `#BD433F` (nekoko-red)
- Background: `#FAFAF7` (nekoko-cream)
- Text dark: `#2E2E2E` (nekoko-dark)
- Neutral gray: `#CFCFCF` (nekoko-gray)
- Font: Inter, all weights, loaded via Google Fonts in `functions.php`

---

## Custom plugin: `nekoko` (aka NekoKo Core)

Path: `wp-content/plugins/nekoko/`

Owns everything domain-specific: CPTs, taxonomies, blocks, shortcodes, moderation, roles, review + booking systems, email templates.

### Directory layout

```
plugins/nekoko/
├── nekoko.php                    — plugin bootstrap, load order
├── includes/
│   ├── class-nekoko-cpt.php              — Job CPT
│   ├── class-nekoko-booking-cpt.php      — Booking CPT
│   ├── class-nekoko-review-cpt.php       — Review CPT
│   ├── class-nekoko-taxonomies.php       — 5 taxonomies + ACF field group for category image
│   ├── class-nekoko-roles.php            — provider + customer roles
│   ├── class-nekoko-moderation.php       — admin moderation dashboard
│   ├── class-nekoko-emails.php           — outbound email wrappers
│   ├── class-nekoko-review-token.php     — booking-gated review token flow
│   ├── class-nekoko-safety-popup.php     — 90-day safety modal
│   ├── blocks/
│   │   └── class-nekoko-blocks.php       — ACF-registered Gutenberg blocks
│   └── shortcodes/
│       ├── class-shortcode-provider-registration.php
│       ├── class-shortcode-provider-dashboard.php
│       └── class-shortcode-reviews.php
├── blocks/
│   ├── hero/render.php
│   ├── search-bar/render.php
│   ├── top-categories/render.php
│   ├── offer-preview/render.php
│   ├── request-preview/render.php
│   ├── featured-jobs/render.php          — (block registered as acf/nekoko-featured-jobs)
│   └── job-categories/render.php         — (block registered as acf/nekoko-job-categories)
└── templates/
    ├── emails/
    │   └── review-request.php
    ├── admin/
    │   ├── moderation-dashboard.php
    │   ├── pending-providers.php
    │   └── pending-jobs.php
    └── shortcodes/
        ├── booking-form.php
        ├── booking-form-success.php
        ├── provider-dashboard.php
        ├── provider-profile.php
        ├── registration-checkboxes.php
        ├── reviews.php
        ├── safety-popup.php
        └── job-card.php
```

---

## Custom Post Types

### Job (`job`)

Public listing. Any provider can create; auto-forced to `pending` status unless the author is an admin (see `Nekoko_CPT::force_pending_for_non_admins()`).

- Slug: `jobs` (archive at `/jobs/`, single at `/jobs/[slug]/`)
- Author: the provider user ID
- Supports: title, editor, thumbnail, author, custom-fields
- Statuses: standard WP + `pending` for moderation queue

**Note on legacy:** an earlier iteration used `service_listing` as the CPT slug. It was renamed to `job` in the 2026-06-19 rebuild. Any posts still tagged `service_listing` in the database are orphaned and will not appear on the site — they need migration to `job` or deletion.

### Booking (`nekoko_booking`)

Booking request from a customer to a provider. Non-public; visible only to the customer, the provider, and admins.

Meta keys:

| Key | Type | Notes |
|---|---|---|
| `_booking_provider_id` | int | User ID of the provider |
| `_booking_customer_name` | string | |
| `_booking_customer_email` | string | |
| `_booking_date` | string `Y-m-d` | Requested service date |
| `_booking_message` | string | Customer's message to provider |
| `_booking_status` | string | `pending`, `confirmed`, or `completed` |

**Known gap:** `_booking_status` is set to `pending` on creation. No code anywhere transitions it to `confirmed` or `completed`. See `booking-flow.md` for detail.

### Review (`nekoko_review`)

A single review of a provider. Tied to the provider's profile, not the job listing.

- 1–5 star rating
- Text comment
- Booking-gated: created only via a secure token issued 5 days after a completed booking

**Known gap:** `Nekoko_Review_CPT::save()` inserts with `post_status = 'publish'` — no moderation queue. See `review-system.md`.

---

## Taxonomies

All five are registered against the `job` CPT.

| Slug | Constant | Purpose |
|---|---|---|
| `job_category` | `Nekoko_Taxonomies::CATEGORY` | Primary service category (Umetnost, Lepota, Zdravlje, Životinje, Zabava, Ostalo) |
| `job_subcategory` | `Nekoko_Taxonomies::SUBCATEGORY` | Optional finer breakdown |
| `job_tag` | `Nekoko_Taxonomies::TAG` | Free-form tags |
| `job_city` | `Nekoko_Taxonomies::CITY` | Geography |
| `job_type` | `Nekoko_Taxonomies::TYPE` | Ponuda (offer) or Potražnja (request) |

### Uniqueness enforcement

`class-nekoko-taxonomies.php` hooks `pre_insert_term` and refuses any new term whose name matches an existing term in the same taxonomy in a case-insensitive comparison. Prevents duplicates like "Beograd" vs "beograd".

### Seeded terms (idempotent, runs on init)

- `job_category`: Umetnost, Lepota, Zdravlje, Životinje, Zabava, Ostalo
- `job_type`: Ponuda, Potražnja

**Descriptions** for `job_category` terms are not seeded from code — the `wp_insert_term()` calls pass only the term name. If descriptions are wanted (e.g. per US 5.1), they must be added manually in wp-admin under **Jobs → Categories**.

### Custom fields on taxonomies

Registered via `Nekoko_Taxonomies::register_acf_image_fields()` (requires ACF Pro):

- **Slika / ikonica kategorije** (`job_category_image`) — image field on each `job_category` and `job_subcategory` term. Used by the Featured Categories block to render the category icon.

Access from PHP: `get_field('job_category_image', 'job_category_' . $term->term_id)`.

---

## User roles

Registered in `class-nekoko-roles.php`:

- `provider` — can create Jobs (auto-pending until admin approves)
- `customer` — can submit booking requests and reviews

Standard `administrator` moderates both.

Provider registration goes through `Nekoko_Shortcode_Provider_Registration`; new users are set to `_nekoko_provider_status = pending` until moderation approves them.

---

## Custom Gutenberg blocks

Two parallel systems currently exist. **The ACF-based system is the target architecture** per current spec.

### Target: ACF-registered blocks (in `nekoko` plugin)

Registered in `includes/blocks/class-nekoko-blocks.php` via `acf_register_block_type()`. Require ACF Pro to be active.

| Block | Where used |
|---|---|
| `acf/nekoko-hero` | Homepage hero section |
| `acf/nekoko-search-bar` | Homepage search |
| `acf/nekoko-top-categories` | Homepage — used twice (Ponuda categories + Potražnja categories) |
| `acf/nekoko-offer-preview` | Homepage — Ponuda live cards |
| `acf/nekoko-request-preview` | Homepage — Potražnja live cards |
| `acf/nekoko-featured-jobs` | Configurable featured-jobs picker (up to 5 jobs, choose type + reorder) |
| `acf/nekoko-job-categories` | Configurable featured-categories picker (choose type + up to 6 categories) |

Category images render from the `job_category_image` ACF field on each term — set once per taxonomy term, not per block instance.

### Interim: native Gutenberg blocks (in `nekoko-child` theme)

Registered in `themes/nekoko-child/inc/blocks.php` via `register_block_type()`:

- `nekoko/featured-categories`
- `nekoko/featured-jobs`

These were built before ACF Pro was in the stack and served as a fallback. Now that ACF Pro is active and the ACF blocks work, these can be removed from the theme.

**Cleanup task:** remove `themes/nekoko-child/inc/blocks.php` and any homepage references to `nekoko/*` blocks; verify homepage still renders correctly using the `acf/*` equivalents.

---

## Shortcodes

Registered in `plugins/nekoko/includes/shortcodes/`:

| Shortcode | Purpose |
|---|---|
| `[nekoko_provider_registration]` | Provider registration form + login |
| `[nekoko_provider_dashboard]` | Provider dashboard (bookings, listings, completed tab) |
| `[nekoko_reviews]` | Provider profile reviews list + submission form |

---

## Bootstrap plugin

`plugins/nekoko-bootstrap/nekoko-bootstrap.php` is a one-time site-setup script:

1. Activates the required plugin list
2. Sets the child theme (`nekoko-child`)
3. Sets permalink structure to `/%postname%/`
4. Sets `blogname`, `blogdescription`, `timezone_string` (Europe/Belgrade)
5. Writes WP Mail SMTP configuration (`from_email = support@nekoko.rs`, `from_name = NekoKo`)

Guarded by an option flag; runs only once per environment. Safe to keep active.

**Known gap:** the SMTP `pass` value on line 85 is blank. Until a real password is set there, all `wp_mail()` calls fail silently — booking confirmation emails, review request emails, admin notifications, all blocked.

Was previously a must-use plugin; converted to a regular plugin per the "no mu-plugins" rule.

---

## Known architectural gaps

Documented here so any developer picking up work sees them immediately:

1. **Booking status never leaves `pending`** — see `booking-flow.md`.
2. **Review system has no moderation queue** — reviews publish directly. See `review-system.md`.
3. **SMTP password missing** — all email delivery blocked. See `deployment.md`.
4. **Interim native Gutenberg blocks in theme need removal** — now that ACF Pro is active, `themes/nekoko-child/inc/blocks.php` is redundant.
5. **Seed script (`wp-content/nekoko-seed.php`) is broken** — references legacy `service_listing` CPT and `service_category` taxonomy, and calls `remove_filter()` for a filter that doesn't exist. Needs rewrite before it can be used again.
6. **Category descriptions not seeded from code** — if descriptions are required (US 5.1), add them via wp-admin.
