# NekoKo.rs — Review system

How customer reviews get from a completed booking to a provider's public profile. Documents the token-gated flow and three current blockers.

Last verified against the codebase: 2026-07-14.

---

## Core principle

**Reviews are tied to the provider, not the job listing.**

This is the platform's central differentiator. On NekoKo, customers choose *who* they hire, so trust signals must accumulate on the person. A provider's average rating and review history live on their profile page and follow them across every listing they publish. If they take a job down and post a new one, the reviews stay.

This is different from most marketplaces (where reviews attach to individual listings or transactions) and is a design choice, not an oversight.

---

## Architecture

Three moving parts:

1. **Review CPT** (`nekoko_review`) — stores each review as a WP post
2. **Review Token** — a one-time secure token that authorizes a specific customer to leave a specific review
3. **Provider profile page** — the public surface where reviews render, with average star rating

### Review CPT

Class: `Nekoko_Review_CPT` in `includes/class-nekoko-review-cpt.php`.

Each review post stores:

- Post title: auto-generated (customer name + provider)
- Post content: the review text
- Post author: the customer who wrote it
- `job_category` taxonomy: stamped from the job at save time, so a provider's average rating can be filtered by category on their profile
- Meta: star rating (1–5), provider user ID, source booking ID

The provider's average rating and review count are cached in provider user meta, recalculated on every save.

### Review Token

Class: `Nekoko_Review_Token` in `includes/class-nekoko-review-token.php`.

A token is:

- Cryptographically random
- Bound to one specific booking and one specific customer email
- Valid for a limited window
- Single-use (invalidated after submission)

The customer receives a link like `/ostavi-recenziju/?token=xxxxx` in an email. Clicking the link takes them to the review form, which reads the token, validates it, and pre-fills the provider and category.

### Provider profile page

Public URL per provider. Displays:

- Provider's name, bio, photo
- Average star rating
- List of published reviews with star, comment, category, date
- Links to the provider's currently-published Job listings

Every Job's single-page template (`single-job.php`) links to the provider's profile page.

---

## The intended flow (end-to-end)

1. Customer submits a booking request → status `pending`
2. Provider fulfills the service → provider (or admin) marks the booking `completed`
3. On the completion transition, `Nekoko_Review_Token::schedule_email()` schedules a cron event 5 days out
4. Cron fires after 5 days → email sent to the customer with a tokenized review link
5. Customer clicks link → review form loads with token
6. Customer submits star + text → `Nekoko_Review_CPT::save()` stores the review
7. Review appears on the provider's profile → average rating recalculated
8. Token is invalidated so it can't be reused

---

## ⚠️ Blockers

**Three separate gaps break this flow end-to-end. Fixing any one alone is not sufficient.**

### 1. Booking status never reaches `completed`

`Nekoko_Review_Token::validate()` refuses tokens for any booking whose `_booking_status` is not `completed`. But nothing in the codebase ever transitions a booking past `pending` — see `booking-flow.md`.

Consequence: even a perfectly delivered token from a perfectly sent email will fail validation. The customer sees a generic "Nešto nije u redu s ovim linkom" error.

**Fix:** implement the completion action (a Provider Dashboard button, or an admin bulk action) that sets `_booking_status = 'completed'` and calls `Nekoko_Review_Token::schedule_email( $booking_id )`.

### 2. Review submission has no moderation queue

`Nekoko_Review_CPT::save()` inserts new reviews with `post_status = 'publish'` — they go live immediately. The user-facing confirmation says: *"Hvala! Vaša recenzija je objavljena."*

The current spec (see US 4.4 in Jira) implies a moderation step: reviews should sit in a pending queue until an admin approves them.

**Fix (small):** change `save()` to insert with `post_status = 'pending'`, and change the confirmation message to *"Hvala! Vaša recenzija je poslata na odobrenje."* — moderation UI in wp-admin (Reviews list) will show the pending queue automatically.

### 3. Email delivery blocked

Even after the two gaps above are fixed, the review request email never leaves the server. `Nekoko_Emails::send_review_request()` calls `wp_mail()`, which fails silently because the SMTP configuration in `nekoko-bootstrap.php` has an empty password field.

See `deployment.md` for the SMTP setup.

---

## What works today

- Review CPT is registered and functional at the storage layer
- Provider profile page renders correctly, displays existing reviews and average rating
- `job_category` stamping from job to review works
- Token generation, validation logic (given valid inputs), and single-use invalidation all work
- 5-day cron scheduling works — `wp_schedule_single_event()` fires correctly

## What doesn't work today

- The end-to-end flow — blocked by three separate issues above
- Manual review submission (bypassing token) — not intentionally supported and no admin path exists
- Moderation queue — reviews are published on save

---

## History note

An earlier implementation (before 2026-06-20) let customers leave open reviews directly on each Job listing page — no booking gate, no token. That was corrected on 2026-06-20 after an audit: it violated the "reviews follow the person, not the listing" principle. Commits `7257343`, `6d4f477`, `8991ec1`, `2dc9efc` made up the rewrite. The token flow, provider profile page, and CPT structure documented above date from that rewrite.
