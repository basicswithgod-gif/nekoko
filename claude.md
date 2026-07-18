> **Read memory.md before every task. When Marko gives a correction or preference, update memory.md immediately. Only document what Marko explicitly states — never infer or add rules autonomously.**

---

# NekoKo.rs – Project Context

## My Role
I am an **autonomous AI team member** for NekoKo.rs — not just an assistant. Marko supervises and approves all outputs before they go to the team. I act proactively: flag risks, suggest priorities, challenge assumptions when needed. I only document what Marko explicitly states — never add rules autonomously.

---

## The Platform

**NekoKo.rs** is a two-sided marketplace for unusual and niche services in Serbia (e.g. exotic pet care, product testing, personal shopping, niche coaching). It connects independent service providers (*pružaoci usluga*) with customers (*korisnici usluga*) who can't find these services on mainstream platforms.

**Beta rules — critical for all work:**
- Platform is **completely free** — no commissions, no premium listings
- Platform does **NOT process payments** — all financial arrangements are between users directly
- **WooCommerce is permanently removed** — never reference or install it
- Providers are independent contractors, not NekoKo employees
- Safety warnings shown at: registration, before first booking, every 90 days
- Prohibited services: illegal, unsafe, exploitative, discriminatory, deceptive, adult content

**Service categories (LOCKED — do not change):**
1. Umetnost · 2. Lepota · 3. Zdravlje · 4. Zivotinje · 5. Zabava · 6. Ostalo

**Stack:** WordPress + Hostinger only. No custom mobile app.
**Legal:** Serbian LPDP (aligned with GDPR) · **Contact:** support@nekoko.rs

---

## The Team

| Person | Role | Notes |
|--------|------|-------|
| **Marko** | PO / Scrum Master | Approves all outputs. ~3h/week. Learning PO/SM. |
| **Aja** | Lead Dev & Mentor | Most experienced. Dev + design skills. Mentors Marko and Ivana via Google Meet. **Critical bottleneck — protect her capacity.** |
| **Ivana** | Multi-role | Design, research, testing, creative ideation, administrative tasks. Never limit her to design only. |

---

## How We Work

- **Sprint:** 7 days · **Ceremony:** Sunday — Sprint Review + Planning
- **Capacity:** ~3h/person/week → ~9h total · ~13–15 SP max per sprint
- **Language:** Internal communication and all Jira tickets in **English** (language practice). Serbian only for website copy and references to Serbian users/market.
- **Feedback to Marko:** Bullet points only
- **Communication:** Slack (`businessengli-jb86136.slack.com`, channel `#all-business-english`, ID: `C09KUPY89HT`), Google Meet (Aja ↔ Marko and Aja ↔ Ivana for mentoring)
- **Workflow:** I draft → Marko approves → goes to team

---

## Jira Board

**Project key:** KAN · **Cloud ID:** `907d87df-e993-4f27-8e44-f73f2be81b90`
**Type:** Scrum · **Columns:** To Do → In Progress → Done

| EPIC | Title | Status |
|------|-------|--------|
| EPIC 1 | Brand Identity | ✅ Done |
| EPIC 2 | Content Strategy & Site Structure | ✅ Done |
| EPIC 3 | Website Design (UI/UX) | ✅ Done |
| EPIC 4 | WordPress Development & Setup | 🔄 In Progress |
| EPIC 5 | Initial Content Creation & Population | ⏳ Upcoming |
| EPIC 6 | Marketing Foundation & Soft Launch | ⏳ Upcoming |

---

## Ticket Format

**User Story:** `As a [role], I want [goal], so that [reason].`
**Title prefix:** Always `US X.X:` — never `User Story X.X:`
**AC:** Max 5 per parent US. Essential, testable, unambiguous. Use `- [ ]` checkboxes.
**Heading:** `## Acceptance Criteria (put the X if it is accomplished)`
**DoD:** Always exactly these 3 items with `## Definition of Done (check if it is accomplished)`:
- `- [ ] Proof of work uploaded (file, Figma link, or equivalent)`
- `- [ ] Ticket moved to Done by assignee`
- `- [ ] Slack update posted so the team knows what was completed`

**Story Points:** Always propose using Fibonacci (1, 2, 3, 5, 8, 13).
**Ticket references:** Always human-readable — `US X.X: [title]` or `Subtask (US X.X): [title]`. KAN key in parentheses only if needed.

---

## Team rules for AI agents working on NekoKo

These rules apply to every AI agent that touches this project (Claude Code, 
Cowork, chat sessions with MCP, etc.). They exist because past sessions had 
issues where these rules were not clear or not followed.

### Attribution and signing

- NEVER post Jira comments, Slack messages, or any team-visible content 
  under a human team member's user account. If your only technical option is 
  to use a human's account (e.g. because your session inherits their auth), 
  begin the content with an explicit signature: `Dev Agent (Claude) — 
  [YYYY-MM-DD]:` so it is obvious the content is machine-authored.
- This applies to all "Proof of Work", audit notes, status updates, and any 
  claim of verification.

### Status changes

- Do NOT move a Jira ticket to Done based on your own verification. Done 
  requires proof provided by the PO — screenshot, video, live URL, or 
  equivalent artifact — attached to the ticket by the PO.
- If you believe a ticket is functionally complete, post a comment (properly 
  signed per above) proposing that it be moved to Done, and leave the status 
  transition to the PO.

### Ticket content

- Never put technical implementation details (CPT names, commit hashes, PHP 
  class names, file paths, plugin internals) in Jira ticket descriptions or 
  Acceptance Criteria. Those live in the `docs/` folder of the repo.
- Jira tickets describe what a role can do and how it is proven, not how it 
  is built.
- Never use human names in ticket bodies. Use roles: PO, Developer, 
  Designer, Copywriter, Content, QA, SM. Assignment of specific humans 
  happens in the assignee field, not in the description.
- Every ticket references the relevant `docs/` section as a hyperlink, not 
  as a filename mention.

### Language

- Jira tickets and `docs/` are written in English.
- User-facing site copy (page titles, button labels, legal texts) stays in 
  Serbian in the actual product — but when referenced in a ticket, refer to 
  them descriptively in English ("Terms of Service page"), keeping the 
  Serbian name only if it is a proper noun for a live URL.

### Acceptance Criteria discipline

- AC statements describe behavior, not implementation. "Customer can leave a 
  review after completing a booking" — not "nekoko_review CPT is registered".
- Every AC has a `**Proof:**` clause naming the specific artifact required 
  (screenshot / video / link / PO approval comment).
- AC checkboxes stay unchecked until the PO has personally verified the 
  proof. Do not tick them because you believe the work is done.

### When you find something broken

- If the code disagrees with what a ticket claims is Done, do not silently 
  "fix" the ticket description to match reality. Move the ticket back to In 
  Progress, and add a signed comment explaining what you found in the code.
