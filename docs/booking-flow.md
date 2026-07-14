# NekoKo.rs — Booking flow

End-to-end flow from a customer landing on a job listing to a completed booking. Documents what works, what's stubbed, and what's currently broken.

Last verified against the codebase: 2026-07-14.

---

## Overview

NekoKo does not process payments. A booking is a **request for service**: the customer submits their preferred date and a message; the provider is expected to contact the customer directly to arrange the specifics. The platform's job ends at forwarding the request.

The flow lives across three surfaces:

1. **Public booking form** — on every single Job page (`/jobs/[slug]/`)
2. **Provider dashboard** — where the provider sees incoming requests
3. **Booking CPT** — where each request is stored (`nekoko_booking`)

---

## Customer-facing flow

Rendered by `class-shortcode-provider-dashboard.php` on the Job single template (`themes/nekoko-child/single-job.php`).

### Step 1 — Date and contact info

Fields:
- Preferred service date
- Customer name
- Customer email

### Step 2 — Message

Free-text field. Customer describes what they want.

### Step 3 — Review

Displays a summary of the entered information so the customer can confirm before sending.

### Step 4 — Success confirmation

Rendered by `templates/shortcodes/booking-form-success.php`. Message shown:

> "Zahtev je poslat! Pružalac usluge će te kontaktirati u roku od 48 sati.
> Kontaktiraj provajdera direktno. NekoKo ne posreduje u plaćanju niti u sporovima između korisnika."

This screen reinforces the "we don't process payments or mediate disputes" position.

---

## Booking storage

Each submission creates one `nekoko_booking` post. It is not public. Only the customer (via email confirmation), the provider (via dashboard), and admins see it.

Meta stored at creation:

| Key | Value |
|---|---|
| `_booking_provider_id` | The Job's author (provider user ID) |
| `_booking_customer_name` | From step 1 |
| `_booking_customer_email` | From step 1 |
| `_booking_date` | Requested service date (`Y-m-d`) |
| `_booking_message` | From step 2 |
| `_booking_status` | `pending` |

---

## Status lifecycle (intended)

```
pending  →  confirmed  →  completed
```

- **pending** — new request, provider has not acknowledged
- **confirmed** — provider has agreed to the booking
- **completed** — the service has been delivered

The Provider Dashboard (`templates/shortcodes/provider-dashboard.php`) has three tabs matching these states: "Novi zahtevi", "Potvrđeni", "Završeni".

---

## ⚠️ Critical gap: status never advances

`_booking_status` is written **once**, at booking creation, with value `pending` (see `class-nekoko-booking-cpt.php` line 82). There is no code path anywhere in the plugin that transitions it to `confirmed` or `completed`.

Impact:

1. **Provider Dashboard's "Potvrđeni" and "Završeni" tabs are always empty**, regardless of how many bookings the provider has fulfilled in real life.
2. **The review token flow is blocked** — see `review-system.md`. `Nekoko_Review_Token::validate()` refuses to issue tokens for bookings that aren't `completed`.

What's needed:

- A UI in the Provider Dashboard (or admin) with two actions per booking: "Potvrdi" (→ `confirmed`) and "Označi kao završeno" (→ `completed`).
- A hook that fires when `_booking_status` becomes `completed`, so it can trigger `Nekoko_Review_Token::schedule_email()` for the 5-day review request.

Note: the existing `Nekoko_Review_Token::on_booking_saved` handler is keyed to `save_post_nekoko_booking` for **new** posts only — a post-save update won't retrigger it. The completion action needs its own hook.

---

## Email touch points

All email sending goes through `Nekoko_Emails` → `wp_mail()`. Currently blocked because the SMTP password in `nekoko-bootstrap.php` is empty.

Intended emails in this flow:

| Trigger | Recipient | Content |
|---|---|---|
| Booking created | Provider | New request notification with customer details |
| Booking created | Customer | Confirmation that request was sent |
| Booking marked `completed` | Customer | Review request with tokenized link (5-day delay via cron) |

None of these currently deliver. All fail silently. See `deployment.md` for the SMTP setup gap.

---

## What works today

- Booking submission form — all 4 steps render and validate
- Booking CPT storage — records are correctly written to the database
- Provider Dashboard's "Novi zahtevi" tab — displays new pending bookings
- Safety disclaimer on success screen — explicit "we don't process payments or mediate disputes" message

## What doesn't work today

- Status transitions past `pending`
- The "Potvrđeni" and "Završeni" dashboard tabs (always empty)
- Any email in the flow (SMTP blocked)
- The downstream review token flow (blocked by both the status gap and the SMTP gap)
