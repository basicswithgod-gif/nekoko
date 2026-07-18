# memory.md – NekoKo Project Memory

> This file is updated immediately whenever Marko gives a correction or preference.
> Read this before every task.
> When two rules contradict, the more recent one applies.
> Claude only documents what Marko explicitly states — never adds rules autonomously.

---

## ⚠️ STROGO PRAVILO — Memory se piše SAMO ovde

**Jedini memory fajl za ovaj projekat je ovaj fajl.**
Sve korekcije, pravila i handoff podaci idu isključivo u `General one multi purpose agent/memory.md`.
Ne kreiraj nove fajlove za memoriju. Ne koristi auto-memory sub-fajlove iz `.claude/projects/memory/` — oni ne zamenjuju ovaj fajl i ovaj fajl ima prednost nad njima.

---

## Corrections & Preferences Log

### 2026-04-16 — AC/DoD format (Marko — FINAL, confirmed via US 5.2)

AC and DoD items must use `- [ ]` checkbox brackets. Headings must include the instruction suffix.
- CORRECT: `## Acceptance Criteria (put the X if it is accomplished)`
- CORRECT: `## Definition of Done (check if it is accomplished)`
- CORRECT: `- [ ] Item text`
- WRONG: headings without suffix · WRONG: `- Item text` without checkbox

### 2026-04-16 — US title format (Marko)

User Story titles must use `US X.X:` prefix. Never `User Story X.X:`.
Titles must be readable at a glance without opening the ticket.

### 2026-04-16 — Deletion rules (Marko)

Tickets for WooCommerce, Stripe, payment gateways, or checkout features must be flagged as
deletion candidates and presented to Marko for approval before deleting.

### 2026-04-18 — Ivana's role (Marko)

Ivana covers design, research, testing, creative ideation, and administrative tasks.
Never assign or describe her work as design-only.

### 2026-04-18 — Ticket reference format (Marko)

Never use KAN numbers alone. Always use human-readable names. Format:
- User Story: `US X.X: [title]` · Subtask: `Subtask (US X.X): [title]` · KAN key in brackets only if needed

### 2026-04-18 — Skill file scope (Marko)

Keep skill files small and focused — one operation per file.

### 2026-05-12 — Final service category list LOCKED (Marko + team approval)

Final 6 categories for NekoKo.rs beta: Umetnost, Lepota, Zdravlje, Zivotinje, Zabava, Ostalo.
Decision gate is closed. All tickets referencing category structure must use this exact list.
KAN-107 moved to Done on 2026-05-12.

### 2026-06-13 — Jira AC gate rule (Marko — applies to ALL agents)

A subtask cannot move to Done if ≥1 AC is unfulfilled.
A US Story cannot move to Done if ≥1 subtask is not Done.
When an AC is blocked and cannot be resolved by the current agent:
- Mark it `! AC` in the ticket description
- Add a blocking comment: what is blocked, why, and who owns the unblock
- Move the ticket back to In Progress

### 2026-06-13 — Proof of work must always include a link (Marko — applies to ALL agents)

Every ticket moved to Done must have a comment with a direct link to what was done:
GitHub commit URL, live page URL, Google Doc link, or Figma link.
"Done" without a link is not acceptable proof of work.

### 2026-06-13 — Agent responsibility boundaries (Marko — applies to ALL agents)

Each agent does only what belongs to their role. When a task belongs to another agent, mark the AC with `! AC` and state who owns it. Never fake-complete someone else's work.

- Code, WordPress, deployment → Dev Agent
- Figma comparison, viewport QA, theme/font final approval → Design Agent (Aja)
- Provider guides, onboarding emails, category copy in Serbian → Content Agent
- Legal page review + approval, provider recruitment, SMTP credentials, approval checklists → Marko

### 2026-06-13 — SMTP password is a hard blocker (Marko)

nekoko-bootstrap.php line 85 has `"pass" => ""` — SMTP password is empty.
All email-delivery ACs are blocked until Marko fills the password on Hostinger.
Never mark an email delivery AC as Done without a screenshot of a successfully received test email.

### 2026-06-13 — Synthetic/seed data does not fulfill real-data ACs (Marko)

Dev-created synthetic data satisfies technical setup ACs only.
ACs that require real content ("10 real providers from Marko's network", "real profile photos", "approved final copy") must be marked `! AC` and assigned to Marko or the appropriate agent.

### 2026-06-13 — Hostinger deploy is a hard blocker for production ACs (Marko)

Any AC referencing https://nekoko.rs, server-side permissions, LiteSpeed Cache, or live-server verification cannot be marked Done until Hostinger deploy happens.
Mark with `! AC`: "BLOCKED: Hostinger deploy pending."

### 2026-06-13 — Ne pitaj Marka za odluke tokom izvršavanja zadatka (Marko)

Do not ask Marko for decisions during task execution. Use best judgement and proceed.
Document decisions made in the ticket comment for Marko's async review.

### 2026-06-13 — Commit hash alone is not a proof-of-work link (Marko — applies to ALL agents)

A bare commit hash (e.g., `2dab043`) is not a direct link. Proof of work requires a full URL:
- WRONG: `2dab043` in a comment
- CORRECT: `https://github.com/basicswithgod-gif/nekoko/commit/2dab043`

If a comment has commit hashes without full URLs, add a follow-up comment with complete GitHub links.

### 2026-06-13 — Parent DoD must be consistent with subtask DoDs (Marko — applies to ALL agents)

When reviewing a parent US, cross-check each DoD item against all subtask DoDs.
If any subtask has a DoD item unchecked (`[ ]`), the parent cannot have that item checked (`[x]`).
Most common error: "Slack update posted" — left `[ ]` on subtasks, incorrectly `[x]` on parent.

### 2026-06-13 — Done tickets must be actively verified, not assumed correct (Marko — applies to ALL agents)

When reviewing a Done ticket, verify each `[x]` AC was actually completed by the responsible agent.
If an AC belongs to Marko (legal review, approval, SMTP credentials, checklists) and there is no evidence it was done — uncheck it, mark `! AC`, and move the ticket back to In Progress.
A dev agent proof-of-work comment flagging "Za Marko: X still needed" means that AC is **not done** — not a formality to ignore.

### 2026-06-13 — Wait for explicit execution approval before modifying skill/memory files (Marko)

When proposing updates to skill files or memory.md, always present the analysis first and wait for Marko's explicit command to execute. Presenting the analysis does NOT constitute approval to proceed.

### 2026-06-15 — BOM check must scan ALL custom PHP files (confirmed via audit)

A task saying "fix BOM in functions.php" does not mean only that file has a BOM.
In this project 6 files had BOM beyond the one reported: `nekoko-bootstrap.php`, `footer.php`, `single-service_listing.php`, `nekoko-seed.php`, `index.php` (WP root).
Every BOM fix session must start with a full scan of all custom PHP files. Fix all, then verify REST API.
WP root `index.php` fix is local only — do NOT commit it (WP core file).

### 2026-06-15 — Homepage content lives in DB as Gutenberg blocks (not in PHP template)

`template-homepage.php` now calls `the_content()` only. All Serbian copy (hero, categories, steps) is stored in `wp_posts.post_content` for page ID 13 as Gutenberg block markup.
Do NOT add hardcoded Serbian copy back to the template file.
Gutenberg editor showing empty content on a custom template = expected when template has no the_content() call. This is NOT a bug.

### 2026-06-19 — Architecture rebuild per Aja's first review (implemented, committed to develop)

All future dev/QA work must use the new architecture. Old references are obsolete:

**Theme:** Hello Elementor parent (no Elementor plugin — Gutenberg only) + nekoko-child.
Old: Astra parent. Never revert to Astra.

**CPT:** `job` (rewrite slug: `jobs`). URLs: `/jobs/`, `/jobs/[slug]/`.
Old: `service_listing`, `/listing/`. All 30 posts migrated.

**Taxonomies (5):** `job_category`, `job_subcategory`, `job_tag`, `job_city`, `job_type`.
Old: single `service_category`. City now stored as `job_city` taxonomy (not `_nekoko_grad` meta).

**Code structure:** All business logic in `plugins/nekoko/` (modular — one class per feature).
Old: `functions.php` monolith. Never put business logic back in functions.php.

**Bootstrap plugin:** `plugins/nekoko-bootstrap/nekoko-bootstrap.php`. SMTP password EMPTY — line with `"pass" => ""` — Marko fills before Hostinger deploy.
Old: mu-plugins/nekoko-bootstrap.php. mu-plugins directory no longer exists.

**Active plugins:** 15 total. Custom: `nekoko`, `nekoko-bootstrap`. All others: third-party (gitignored).

**SCSS:** `themes/nekoko-child/scss/` — 13 partials. Compile: `npx sass --style=compressed --no-source-map scss/main.scss assets/css/style.min.css` from nekoko-child directory. Commit compiled `style.min.css` alongside source.

**Git:** repo root is `C:\Users\Marko\Local Sites\nekoko\app\public`. Branch: `develop`.

### 2026-06-19 — Hello Elementor resets button background — always use !important

Hello Elementor parent theme sets `button { background: transparent }` with high specificity.
Any `.nekoko-btn { background: color }` without `!important` will be overridden and show transparent.
Fix is in `scss/_buttons.scss` and `scss/_categories.scss` — always use `background: $color !important`.
Same applies to any button-like element that fights Hello Elementor's reset.

### 2026-06-19 — nekoko.local is HTTPS only (not HTTP)

Local by Flywheel runs nekoko.local on HTTPS (port 443) only. Port 80 is closed.
Always use `https://nekoko.local`. curl requires `--insecure` or `--ssl-no-revoke`.
Chrome MCP handles SSL internally — use navigate to https URLs directly.

### 2026-06-19 — Plugin install without browser: curl + python zip + MySQL active_plugins

When browser is unavailable for plugin install:
1. `curl -L --ssl-no-revoke -o /tmp/plugin.zip https://downloads.wordpress.org/plugin/slug.zip`
2. Python `zipfile.ZipFile().extractall('...wp-content/plugins/')` to extract
3. MySQL UPDATE on `wp_options` to add plugin path to `active_plugins` serialized array
Note: activation hooks (table creation etc.) do NOT fire via MySQL-only — use browser for those.

### 2026-06-20 — Provider profile + category-segmented reviews — IMPLEMENTIRANO (handoff)

**Status: Kod commit-ovan na `develop`, nije push-ovan na GitHub, nije funkcionalno testiran.**

**4 commita na `develop`:**
- `7257343` — `nekoko_review` CPT + provider profile URL routing
- `8991ec1` — Booking-gated review token + 5-day cron email trigger
- `6d4f477` — Review shortcode + template rewrite (CPT-based, token-gated)
- `2dc9efc` — Provider profile page template + "Pogledaj profil" link u listing sidebaru

**Šta je izgrađeno:**
- `nekoko_review` CPT: jedan post = jedna recenzija. Meta: `_review_provider_id`, `_review_job_id`, `_review_booking_id`, `_review_reviewer_name`, `_review_rating`. Tekst u `post_content`. `job_category` taxonomy stamped sa joba pri save-u. `_nekoko_avg_rating` i `_nekoko_review_count` i dalje cached na job postu (search filter i job card rade nepromenjeno).
- Token sistem: `save_post_nekoko_booking` hook generiše 32-char token → cron +5 dana → email sa `/jobs/[slug]/?review_token=TOKEN`. Token expires 30 dana, jedan use, booking mora biti `completed`.
- Profil URL: `/provajder/{user_nicename}/` via rewrite rule. Template: `plugins/nekoko/templates/provider-profile.php`. 404 ako user nije provider. Prikaz: avatar, ime, lokacija, bio, review tabovi po `job_category`, aktivni listinzi (max 6).
- `nekoko.php` verzija bumped na `1.1.0` (triggeruje one-time `flush_rewrite_rules()`).

**Novi fajlovi:** `class-nekoko-review-cpt.php`, `class-nekoko-review-token.php`, `class-nekoko-provider-profile.php`, `templates/provider-profile.php`, `templates/emails/review-request.php`

**Izmenjeni fajlovi:** `nekoko.php`, `class-shortcode-reviews.php`, `reviews.php` (template), `class-nekoko-emails.php`, `single-job.php`

**Nepromenjeni (kompatibilni):** `job-card.php`, `class-shortcode-search-filter.php`

**Migration check:** 0 postojećih `_nekoko_reviews` zapisa (fresh dev install, nema seed podataka). Nema migracije.

**Funkcionalni testovi: NISU pokrenuti.** Sledeća sesija treba da proveri:
- `/provajder/[slug]/` resolves i 404-uje kako treba
- Submit booking → token se kreira → cron zakazan
- `?review_token=TOKEN` prikazuje formu → submit kreira `nekoko_review` post → token marked used
- Drugi posjet sa istim tokenom → "već iskorišćen"
- `_nekoko_avg_rating` update na job postu nakon review-a

**Otvoreni itemi:**
- `_nekoko_location` usermeta: registration forma ne hvata lokaciju — provajderi je pune ručno u WP admin ili čekaju budući profile-edit form
- `nekoko-btn--outline` CSS klasa ne postoji → tab dugmad na profilu nemaju vizuelnu razliku — Aja dodaje u SCSS
- GitHub push: nije urađen — Marko radi ručno
- Plugin header `Version:` još uvek piše `1.0.0` (kozmetičko, nije funkcionalni problem)

**Judgment calls (van 5 PO odluka):**
- Token expiry: 30 dana (PO nije specificirao)
- Review na listing stranici prikazuje recenzije za taj konkretan job (`_review_job_id`), ne za celu kategoriju provajdera
- Reviewer ime uvek dolazi iz `_booking_customer_name`, nije editabilno
- Token validacija: booking status se proverava u momentu submita (ne u momentu slanja emaila)
- 5-day follow-up email: izgrađen od nule (nije postojao u codebaseu)

### 2026-06-20 — STROGO PRAVILO: Jedan memory fajl, nula novih fajlova (Marko)

**Jedini memory fajl za ovaj projekat je: `C:\Marko\Nekoko_Platform\General one multi purpose agent\memory.md`**

Pravila koja se ne smeju kršiti:
- Sve korekcije, pravila i handoff podaci idu isključivo u ovaj fajl.
- Ne kreiraj nove .md, .txt ili bilo koje druge fajlove za čuvanje memorije — bez obzira na razlog ili context.
- Ne uređuj, ne čitaj i ne pozivaj se na auto-memory sub-fajlove u `.claude/projects/memory/` (npr. `project-context.md`, `feedback-preferences.md`, `project-provider-profile.md`) — to su sistemski fajlovi koji ne zamenjuju ovaj.
- Ako trebaš dodati informaciju u memoriju, dodaš je ovde, na kraju Corrections & Preferences Log sekcije, sa datumom i izvorom.
- Ako pravilo postoji i u ovom fajlu i u auto-memory fajlu, ovaj fajl ima prednost.

### 2026-06-20 — Figma MCP requires editor access, not view-only (2026-06-20)

Figma account `markostefanovic808@gmail.com` has a View-only (Starter) seat.
The Figma MCP returns "Looks like you don't have edit access to this file" for ALL API calls
(`get_metadata`, `download_assets`, `get_design_context`) when using a view-only seat.
This is not transient — it is a seat-type restriction.
Workaround: Use Chrome MCP to screenshot the Figma web app, or request specific node URLs from Marko.
To unlock full MCP: Marko must upgrade Figma account to editor seat.

### 2026-06-20 — Header and footer are brand blue; logo updated to PNG (2026-06-24)

- Header background: `#004682` (brand blue). Nav links white, yellow hover.
- Footer background: `#004682` (brand blue).
- **Logo file (current):** `themes/nekoko-child/assets/images/profile-nekoko.png` — square PNG 1:1, blue background (#004682), white "Neko" + yellow "KO" + yellow magnifier. Used in both header (52×52) and footer (56×56). Blue background blends with blue header/footer.
- `logo-nekoko.svg` ostaje u assets ali više nije referenciran ni u header.php ni u footer.php (od commita 39ea026).
- Logo height: 52px header, 56px footer (HTML width/height attributes + CSS width:auto).

### 2026-07-04 — Page slug updates (nekoko.local)

Page slugs updated to match navigation labels:
- Page ID 5 (O nama): slug changed from "o-nama" to "o-portalu" → http://nekoko.local/o-portalu/
- Page ID 9 (Uslovi koriscenja): slug changed from "uslovi-koriscenja" to "pravila-sajta" → http://nekoko.local/pravila-sajta/

Footer menu "Informacije" updated accordingly:
- "O portalu" links to /o-portalu/
- "Pravila sajta" links to /pravila-sajta/

**Discovered while verifying:** the `footer-info` theme location (nekoko-theme) had **no menu assigned** (`menu: 0` in `/wp-json/wp/v2/menu-locations`), so renaming the WP nav menu items (ID 29) had zero visible effect — the theme was silently using the `fallback_cb` hardcoded array in `footer.php` instead. That fallback array had a pre-existing bug unrelated to today's slug change: it linked to `/pravila` and `/privatnost`, neither of which ever matched a real page slug (correct slugs are `pravila-sajta` and `politika-privatnosti`). Fixed in `nekoko-theme/footer.php` (both the `Informacije` column fallback array and the bottom-bar legal links) and synced to the Local Sites theme copy. Confirmed via front-end DOM check — all footer links now resolve correctly.

## Page Content — Mandatory Protocol

BEFORE touching any page content for any reason:

1. **Backup (via MySQL HEX — WP CLI does not exist in Local by Flywheel):**
   ```python
   r = subprocess.run([MYSQL, "-u", "root", "-proot", "-h", "127.0.0.1", "-P10005", "local",
       "--batch", "--raw", "-e", "SELECT HEX(post_content) FROM wp_posts WHERE ID=[ID]"],
       capture_output=True, timeout=30)
   content = bytes.fromhex(r.stdout.decode("ascii").strip().split("\n")[1]).decode("utf-8")
   with open("backup_page[ID]_YYYYMMDD.txt", "w", encoding="utf-8") as f: f.write(content)
   ```
   Verify backup file is not empty before proceeding.

2. **Write via MySQL UNHEX() — the only confirmed method in Local by Flywheel:**
   ```python
   sql = f"UPDATE wp_posts SET post_content = UNHEX('{content.encode('utf-8').hex()}'), post_modified = NOW() WHERE ID = [ID];"
   subprocess.run([MYSQL, "-u", "root", "-proot", "-h", "127.0.0.1", "-P10005", "local"],
                  input=sql.encode("ascii"), capture_output=True, timeout=60)
   ```
   WP CLI does not exist. WP REST API is an alternative but requires auth token setup. UNHEX() is the confirmed working method.

3. **After ANY UNHEX() write — mandatory Gutenberg verification (two-pass):**
   - Open page in Gutenberg editor
   - **SCROLL ENTIRE EDITOR** (lazy rendering — off-screen blocks don't validate until scrolled)
   - Pass 1 (pre-save): Zero "Block contains unexpected or invalid content" messages for ALL block types
   - Click "Attempt recovery" only on pre-existing NON-paragraph warnings (heading/button)
   - Click "Update" (save)
   - **RELOAD** the editor
   - **SCROLL ENTIRE EDITOR again**
   - Pass 2 (post-save+reload): Zero warnings — if ANY appear, restore immediately from backup
   If paragraph block warnings appear after save+reload → Gutenberg's `save()` output doesn't match stored HTML → restore from backup and investigate the format mismatch before retrying.

4. **ALL pages must be 100% native Gutenberg blocks.**
   No ACF blocks. No wp:html for static content.
   Every text and image must be click-to-edit inline in Gutenberg editor.

---

### 2026-06-20 — Svi novi fajlovi koje Claude Code kreira idu u Nekoko Platform folder (Marko)

`C:\Marko\Nekoko_Platform\` je primarna lokacija za sve nove fajlove.
- Logoi, aseti, exporti, promptovi, skill fajlovi, dokumenti → direktno u `C:\Marko\Nekoko_Platform\`
- Fajlovi koji moraju biti na tehničkoj putanji da bi radili (PHP u pluginu, SCSS u temi, JS u temi) → ostaju na tehničkoj lokaciji, ali kopija ide i u `C:\Marko\Nekoko_Platform\`
- Pravilo važi za sve buduće sesije bez izuzetka.

### 2026-06-19 — Aja's Slack identity

Aja = Aya Romporas. Slack user ID: `U09KRRR13GT`. Email: `aya.romporas@gmail.com`.
Searching Slack for "Aja" returns no results — always search for "Aya Romporas".

### 2026-06-24 — Homepage Gutenberg blocks — IMPLEMENTIRANO (handoff)

**Status: Commit `600d181` na `develop`. Nije push-ovan na GitHub. Funkcionalni test potvrđen via curl.**

**Šta je izgrađeno (5 ACF blokova + theme nav + footer):**
- `acf/nekoko-hero` — 2-kolona layout, cream bg, 4-sliku grid, badge "1.240+ aktivnih oglasa"
- `acf/nekoko-search-bar` — flex layout, dinamički `<select>` iz `get_terms(job_category)`, GET ka `/pretraga/`
- `acf/nekoko-offer-preview` — Ponuda preview, job kartice sa `nekoko-pill--yellow`, placeholder cards kad 0 rezultata
- `acf/nekoko-top-categories` — dual-instance: `tc_type=ponuda` i `tc_type=potraznja`, kategorija slike via ACF image field
- `acf/nekoko-request-preview` — Potražnja preview, kartice LEVO / content DESNO, `nekoko-pill--blue`
- ACF field groups registrovani via `acf_add_local_field_group()` u `class-nekoko-blocks.php` (5 novih blokova, explicit field keys prefix `field_nekoko_*`)
- `acf/load_field/key=field_nekoko_tc_categories` filter za dinamičke checkbox opcije
- `job_category_image` ACF field dodat na `job_category` i `job_subcategory` taxonomy termove u `class-nekoko-taxonomies.php`

**Theme promene:**
- `footer.php` — 4-kolona grid (brand + Ponuda/Potražnja/Informacije nav menus), `nekoko-footer__bottom` copyright bar
- `functions.php` — 4 nova nav locations (footer-ponuda, footer-potraznja, footer-informacije + footer-tagline widget area)
- `header.php` — akcioni dugmadi "Prijava" (outline-white) + "Postavite oglas" (accent/yellow) u `__actions` div
- `nekoko.php` — `hello_elementor_page_title` filter skriva `<h1 class="entry-title">` na front page
- `_homepage.scss` — novi partial (482 linije), `%hp-section-inner`, svih 7 sekcija, responsive breakpoints
- `_buttons.scss` — dodati `--outline-white`, `--accent`, `--red`, `--yellow` varijante (sve sa `!important`)
- `_footer.scss` — kompletan rewrite (4-kolona grid, brand/tagline/bottom-bar)
- `_header.scss` — dodato `&__actions` (flex, gap 8px)

**DB insert:**
- Page ID 13 sadrži 6 Gutenberg blokova via `UNHEX()` MySQL metodu. DB: `local`. content_len = 661.
- Top-categories instance razlikovane `data: {"tc_type":"ponuda"}` i `data: {"tc_type":"potraznja"}` u block comment JSON.

**Verifikacija:**
- Curl potvrđuje sve CSS klase u renderovanom HTML: `nekoko-hp-hero`, `nekoko-hp-search`, `nekoko-hp-preview--offer`, `nekoko-hp-preview--request`, `nekoko-hp-top-cats` (×2), `nekoko-footer`
- `nekoko-pill--yellow` i `nekoko-pill--blue` prisutni, `nekoko-footer__col-heading` ×3 (Ponuda/Potražnja/Informacije)
- Naslov "Pocetna" uklonjen — `hello_elementor_page_title` filter radi
- Screenshot hero + search bar sekcija vidljiv; screenshot top-cats potražnja + footer vidljiv

**Fiksiran open item iz prethodne sesije:**
- `_nekoko_location` registration forma: commit `600d181` hvata lokaciju u provider registration (`nekoko_location` field, validacija, `update_user_meta`).

**Otvoreni itemi:**
- GitHub push: nije urađen — Marko radi ručno
- WP Admin block editor screenshot (ACF fields visible) — nije uhvaćen jer Chrome MCP nije bio dostupan
- Taxonomy kategorija image upload — nema uploada; kategorije prikazuju emoji placeholder (🐾❤️💅🍽️🤝🎨) po slug-u
- Hero sekcija desna strana — placeholder grid (4 sive kutije); Marko uploaduje slike via WP Admin → Stranice → Pocetna → Uredi blok "Hero" → Hero Images

### 2026-06-24 — Homepage vizuelni polish (commit 39ea026)

- **Logo**: `profile-nekoko.png` (plavi kvadrat 52px) koristi se u headeru i footeru umesto SVG-a
- **Primary nav**: Početna → page 13, Ponuda/Potražnja → custom URL (/pretraga/?job_type=...), O portalu → page 5, Kontakt → page 8
- **Card klasa**: nova `nekoko-hp-card` klasa (ne stara `nekoko-job-card`) za homepage preview kartice — kategorija crvena uppercase, cena desno, grad dole-levo
- **Footer menus**: 3 menija popunjena (footer-ponuda term_id=27/tt=28, footer-potraznja term_id=28/tt=29, footer-informacije term_id=29/tt=27); theme_mods ažuriran UNHEX metodom
- **Nove kategorije**: Ljubimci (id=24), Ishrana (id=25), Pomoć (id=26) dodati u job_category; top-categories blokovi pre-selektuju ove 5: [24,13,12,25,26]
- **Serialization nota**: WP serialized PHP string mora imati tačan broj karaktera u s:N prefix. "footer-ponuda" = s:13, "footer-potraznja" = s:16, "footer-informacije" = s:18. Uvek koristi UNHEX() metodu za update wp_options.

### 2026-06-25 — Homepage mora biti 100% native Gutenberg blokovi — ACF blokovi odbijeni (Marko)

Marko je eksplicitno odbio ACF blokove na homepage-u: "Hocu da kliknem i editujem naslov prve sekcije a dobijam prozor sa poljima koji necu."
ACF blokovi u Gutenberg editoru otvaraju popup formu sa poljima — nema inline WYSIWYG editinga.
**Pravilo: sav sadržaj page ID 13 mora biti isključivo native Gutenberg blokovi** (`wp:group`, `wp:columns`, `wp:column`, `wp:heading`, `wp:paragraph`, `wp:buttons`, `wp:button`, `wp:image`, `wp:html`).
ACF blokovi su ZABRANJENI na svim stranicama — ne samo na homepage-u.
Sve stranice moraju biti 100% native Gutenberg blocks.

Implementirano: commit `0155c8b` — homepage (page ID 13) sadrži native Gutenberg markup. Stari ACF pristup (commit 600d181) je ZAMENJEN.
Template: `template-homepage.php` poziva samo `the_content()`. `theme.json` definiše `contentSize: 1200px`.
Kontejner pattern: CSS klasa `nekoko-section-inner` (max-width 1200px, margin auto) — pouzdanije od WP constrained layout sa hello-elementor.

### 2026-06-25 — PHP CLI u Local nema mysqli — nikad ne koristiti za DB operacije (potvrđeno)

`php.exe` (`lightning-services/php-8.2.29+0/bin/win64/php.exe`) nema `php.ini` → nema mysqli → WP bootstrap puca.
Svaki pokušaj DB operacije via PHP CLI vraća: "Your PHP installation appears to be missing the MySQL extension."
**Nikad ne pokretati PHP CLI skripte za DB operacije.** Uvek: `mysql.exe` direktno + UNHEX() metoda za UTF-8 sadržaj.

### 2026-06-25 — Homepage CSS fix patterns (finalni)

**S2 search section — layout:**
Block struktura u page 13: `wp:column 38%` (wp:heading + wp:paragraph) / `wp:column 62%` (wp:html search form).
Outer group ima klasu `nekoko-hp-search-section`. CSS u `_homepage.scss`:
```scss
.nekoko-hp-search-section .wp-block-columns { align-items: flex-start !important; }
.nekoko-hp-search-section .wp-block-column:last-child {
    display: flex !important; align-items: flex-end !important; padding-top: 34px !important;
}
```
`padding-top: 34px` simulira visinu H2 i gura search formu dolje da se poravna sa opisnim tekstom.
`align-items: center` NE radi — WP override-uje `.wp-block-column` layout.

**Hero outline dugme "Kako funkcioniše" — finalni:**
Klase na div wrapperu: `wp-block-button nekoko-btn nekoko-btn--outline`.
Boja: `#004682` (brand blue), `border: 2px solid`, `background: transparent`.
CSS: `.wp-block-button.nekoko-btn.nekoko-btn--outline { border: none !important }` (uklanja WP default border na wrapperu).
`.wp-block-button.nekoko-btn.nekoko-btn--outline .wp-block-button__link { border: 2px solid #004682 !important; color: #004682 !important; }`.

**MySQL — UVEK koristiti HEX() za čitanje sadržaja:**
```python
# ISPRAVNO:
r = subprocess.run([mysql, ..., "-e", "SELECT HEX(post_content) FROM wp_posts WHERE ID=13"])
content = bytes.fromhex(r.stdout.decode("ascii").strip().split("\n")[1]).decode("utf-8")

# POGREŠNO — srpska slova (č,ć,š,ž,đ) postaju ?:
r = subprocess.run([mysql, ..., "-e", "SELECT post_content FROM wp_posts WHERE ID=13"])
content = r.stdout.decode("utf-8", errors="replace")  # ← charset konverzija u latin1
```
MySQL CLI bez `--default-character-set=utf8mb4` konvertuje utf8mb4 u latin1 pri izlazu. `HEX()` vraća ASCII bajtove bez charset konverzije.

### 2026-06-25 — http://nekoko.local radi za Chrome MCP kada https daje privacy error (korekcija)

Nota 2026-06-19 kaže "HTTP port 80 zatvoren — uvek https". **Korekcija potvrđena u sesiji 2026-06-25:**
Kada `https://nekoko.local` prikazuje Chrome "Privacy error" stranicu, Chrome MCP ekstenzija se ne može prikačiti.
`http://nekoko.local` radi za Chrome MCP navigaciju — nema SSL greške, ekstenzija funkcioniše normalno.
Typing "thisisunsafe" na Chrome error stranici ne radi (Chrome blokira unos na error stranicama).
`curl` i dalje zahteva `--insecure` ili `--ssl-no-revoke` za https. Za Chrome MCP: koristiti http ako https prikazuje privacy error.

### 2026-06-25 — browser_batch Chrome MCP: kratak naziv alata unutar poziva, ne puni namespace

Unutar `mcp__Claude_in_Chrome__browser_batch` poziva, sub-alati se navode kratkim imenom.
GREŠKA: `{"name": "mcp__Claude_in_Chrome__computer", ...}` → error "unknown tool mcp__Claude_in_Chrome__computer"
ISPRAVNO: `{"name": "computer", ...}` unutar `browser_batch`.
Puni MCP naziv (`mcp__Claude_in_Chrome__computer`) se koristi SAMO za standalone pozive van `browser_batch`.

---

## HANDOFF — 2026-06-24 (kraj sesije)

### Trenutno stanje `develop` branch-a (nije push-ovan na GitHub)

**Commit log (novi, od poslednjeg pusha):**
- `600d181` — feat: [US 4.X] Homepage Gutenberg blocks + taxonomy images + native nav/footer
- `39ea026` — fix: homepage visual polish — logo, nav, cards, footer links

**Stranica ID 13 (`/pocetna/`, front page):**
⚠️ ZASTARELO — page ID 13 je migriran na 100% native Gutenberg blocks
u sesiji 2026-06-26. Vidi HANDOFF 2026-06-26 za trenutno stanje.

**Primary nav menu (Main Navigation, term_id=8):**
Početna (pg 13) · Ponuda (/pretraga/?job_type=ponuda) · Potražnja (/pretraga/?job_type=potraznja) · O portalu (pg 5) · Kontakt (pg 8)

**Footer nav menus (svi popunjeni):**
- footer-ponuda (term_id=27): Ljubimci · Zdravlje · Lepota · Ishrana · Pomoć → `/pretraga/?job_type=ponuda&job_category=[slug]`
- footer-potraznja (term_id=28): iste kategorije, job_type=potraznja
- footer-informacije (term_id=29): O nama · Kako funkcioniše · Bezbednost · Kontakt · Uslovi korišćenja

**job_category taxonomy termovi (svi):**
ID 11 Umetnost · 12 Lepota · 13 Zdravlje · 14 Životinje · 15 Zabava · 16 Ostalo · 24 Ljubimci · 25 Ishrana · 26 Pomoć

**MySQL baza:** `local` · Port: 10005 · User: root · Pass: root
**MySQL exe:** `C:\Users\Marko\AppData\Roaming\Local\lightning-services\mysql-8.0.35+4\bin\win64\bin\mysql.exe`
**Site URL:** `https://nekoko.local` (HTTPS only, HTTP zatvoren)
**Git root:** `C:\Users\Marko\Local Sites\nekoko\app\public`

### Šta sledeća sesija treba da uradi

**Visoki prioritet:**
1. **Hero slike** — Marko treba da uploada foto kolaž via WP Admin → Stranice → Pocetna → Edit → blok Hero → polje "Hero Images" (repeater, 4 slote). Alternativa: promeniti hero render.php da podržava 1 veliku sliku umesto 4-grida.
2. **Funkcionalni test review toka** — nije testiran od commita 7257343/8991ec1/6d4f477/2dc9efc: booking → token → email → forma → CPT review post
3. **`nekoko-btn--outline` CSS** — tab dugmad na provider profilu nemaju vizuelnu razliku (klasa ne postoji u SCSS). Dodati u `_buttons.scss`.
4. **GitHub push** — Marko radi `git push origin develop` ručno (5 commitova čeka)

**Srednji prioritet:**
5. **Top kategorije slike** — uploadovati slike/ikone za svaku `job_category` via WP Admin → Poslovi → Kategorije → uredi termin → polje "Slika kategorije" (ACF). Dok nema slike, prikazuje emoji fallback.
6. **Footer tagline widget** — Appearance → Widgets → Footer Tagline → dodati text widget sa opisom platforme (trenutno se prikazuje hardcoded fallback paragraf)
7. **Seed data** — dodati test oglase za Ponuda i Potražnja job_type da kartice na homepage-u prikazuju prave podatke (ne placeholder)

**Nema blokadera za front-end QA** — stranica se renderuje kompletno. Chrome MCP nije bio dostupan tokom sesije; verifikacija vršena via `curl -sk https://nekoko.local/`.

### Tehnički napomene za sledeću sesiju

- **CSS klasa za HP kartice**: `nekoko-job-card` — potvrđeno u sesiji 2026-06-26. Napomena: `nekoko-hp-card` iz prethodnog handoffa je bila netačna.
- **Kategorije NISU zaključane** — Marko je u ovoj sesiji dodao Ljubimci/Ishrana/Pomoć izvan originalne liste od 6. Nova lista de facto: Umetnost, Lepota, Zdravlje, Životinje, Zabava, Ostalo, Ljubimci, Ishrana, Pomoć.
- **SCSS kompajliranje**: `cd "C:\Users\Marko\Local Sites\nekoko\app\public\wp-content\themes\nekoko-child" && npx sass --style=compressed --no-source-map scss/main.scss assets/css/style.min.css` — uvek commitovati kompajlirani `style.min.css`.
- **WP options update**: koristiti isključivo UNHEX() metodu za update `wp_options` koji sadrži serializovane PHP nizove. Broj karaktera u `s:N:` mora biti tačan.

---

## HANDOFF — 2026-06-26 (Gutenberg fix page 13)

### Page ID 13 — Status: 100% native Gutenberg, zero warnings ✅

**Šta je urađeno u ovoj sesiji:**
- 29 nested `<p><p>text</p></p>` blokova stripovano → čist `<p>text</p>`. Outer `<p>` atributi sačuvani, NIJE dodavan `textAlign` u JSON (bio je uzrok Gutenberg re-nestiranja pri save-u u WP 6.9.4).
- `wp:html` hardcoded search forma zamenjena sa `<!-- wp:shortcode -->[nekoko_hp_search]<!-- /wp:shortcode -->`.
- Nov shortcode `[nekoko_hp_search]` kreiran u `plugins/nekoko/includes/shortcodes/class-shortcode-hp-search.php`. Dinamički `<select>` iz `get_terms(job_category)`, koristi `kw=` i `kat=` params (matchuje search-results page), akcija ka `/pretraga/`.
- Commit: `47578012cf5b4af0c3237a8b6e772a0ba945dcd6` → https://github.com/basicswithgod-gif/nekoko/commit/47578012cf5b4af0c3237a8b6e772a0ba945dcd6

**Verifikacija (potvrđeno):**
- Gutenberg editor: 0 "Block contains unexpected or invalid content" poruka nakon save + reload (scroll kroz celu stranicu)
- Svi `wp:paragraph` blokovi su inline-editable
- Search forma renderuje na frontendu sa 10 dinamičkih kategorija

**Ključna lekcija — WP 6.9.4 + textAlign na `wp:paragraph`:**
NE dodavati `"textAlign"` u block JSON za `wp:paragraph` ručno via UNHEX(). WP 6.9.4 `save()` funkcija za paragraph+textAlign generiše drugačiji HTML od onog koji UNHEX() write može reproducirati, što uzrokuje validacioni loop. Rešenje: textAlign za kartične cene i naslove sekcija aplikovati via SCSS selektore (`.nekoko-job-card .wp-block-column:last-child p { text-align: right }`), ne inline.

**Preostali otvoreni itemi iz prethodnih sesija (nepromenjeni):**
- GitHub push: Marko radi `git push origin develop` ručno
- Funkcionalni test review toka (booking → token → email → forma → CPT review post)
- `nekoko-btn--outline` CSS klasa za tab dugmad na provider profilu
- Top kategorije slike — upload via WP Admin → Poslovi → Kategorije
- Hero slike — upload via WP Admin → Stranice → Pocetna → Edit → blok Hero
- Seed data za Ponuda/Potražnja kartice na homepage-u

---

## HANDOFF — 2026-06-26 (alignment fix via SCSS)

### Text alignment restored — commit `6ff4664` na `develop`

**Šta je urađeno:**
- `textAlign` bio stripovan sa `wp:paragraph` blokova tokom Phase 2 nested-p fixa (prethodna sesija)
- Popravljeno via SCSS selektori u `scss/_homepage.scss` — bez izmena page ID 13 post_content

**Dodati selektori (potvrđeni u produkciji):**
```scss
.nekoko-section-center p { text-align: center; }         // S2/S4/S6 subtitle paragrafi
.nekoko-job-card .wp-block-column:last-child p { text-align: right; } // cene u karticama S3/S5
```

**Ključne činjenice potvrđene u ovoj sesiji:**
- Kartica klasa: `nekoko-job-card` — `nekoko-hp-card` iz handoffa 2026-06-24 je bila **netačna**
- Subtitle `<p>` nema klasu `wp-block-paragraph` — ima samo `has-text-color`. Selektor mora biti `p`, ne `.wp-block-paragraph`
- Oba selektora verifikovana: serviran CSS sadrži oba pravila, vizuelno potvrđeno na `http://nekoko.local/`

**Commit:** https://github.com/basicswithgod-gif/nekoko/commit/6ff4664
GitHub push: Marko radi ručno.

---

### 2026-07-04 — Heading brand color CRITICAL, wp:table, wp:list ordered, contact form, pages converted (nekoko.local session)

**Heading brand color — CRITICAL:**
Never use inline style color + has-text-color for headings.
WordPress core forces `!important` on all `.has-*-color` classes via theme.json palette,
which silently overrides any inline color — headings render gray, not blue.

Correct method: add class `nk-heading-primary` to `wp:heading` blocks.
CSS rule in `nekoko-theme/assets/css/blocks.css`:
```css
.wp-block-heading.nk-heading-primary { color: #004682 !important; font-weight: 700 !important; }
```
Two-class specificity beats WP core's one-class rule regardless of load order.
Applies sitewide — all pages already updated (O nama, Uslovi, Bezbednost, Politika privatnosti, Kako funkcioniše, Kontakt).

**wp:table block:**
Native `wp:table` block works for data tables in page content.
Table survived UTF-8 conversion cleanly (confirmed on Politika privatnosti page ID 10).

**wp:list ordered:**
For numbered procedures use `wp:list {"ordered":true}` — renders as `<ol>`.
Confirmed working on Kako funkcioniše page ID 6.

**Contact form (WPForms):**
WPForms Lite form ID 130 — "Kontakt forma"
Fields: Ime (text), Email adresa (email), Poruka (textarea, 5 rows)
Embedded on Contact page ID 8 via `wp:shortcode` block: `[wpforms id="130"]`
WPForms post type is NOT REST-exposed — form creation/editing must go via MySQL UNHEX method.
SMTP not configured — form layout confirmed, email delivery not tested.

**Pages converted to native Gutenberg (nekoko.local — this is the site on `nekoko-theme`, tested on the `local` DB via Local by Flywheel; content lives in `wp_posts` on the running site):**
All pages now 100% native Gutenberg blocks, fully editable, root-level blocks only (no group wrapper):
- ID 5  | O nama
- ID 6  | Kako funkcioniše
- ID 8  | Kontakt (+ WPForms shortcode)
- ID 9  | Uslovi korišćenja
- ID 10 | Politika privatnosti
- ID 11 | Bezbednost
Each heading uses `nk-heading-primary` class. All backups saved as `page{ID}_backup_20260704.txt`.

---

## Session log — 2026-07-14: Jira backlog restructuring

### What changed this session

- Full audit of NekoKo Jira EPIC 4 and EPIC 5 against the actual code in 
  `basicswithgod-gif/nekoko`. Found systematic mismatch between what Jira 
  claimed as Done and what the code actually did.
- Created 5 canonical docs in the repo at `docs/`: `architecture.md`, 
  `booking-flow.md`, `review-system.md`, `deployment.md`, and 
  `definition-of-done.md`. Committed as `339af25` on develop branch.
- Established new ticket format: behavioral User Story + AC with `**Proof:**` 
  clause + roles (not names) + hyperlinked reference to `docs/`. Subtasks 
  reduced to one-liner "Done when" + role + reference.
- Rewrote all of EPIC 4 (US 4.1 through 4.6) and EPIC 5 (US 5.1 and 5.2) in 
  the new format, in English, with real GitHub hyperlinks.
- EPIC 1 (Brand identity) has KAN-21 rewritten as pilot; rest pending.
- EPIC 2, 3, 6: not yet rewritten. EPIC 3 has legitimate Figma links from 
  Ivana Zlatanović and Aya Romporas in comments — those must be preserved 
  when rewriting.
- Removed all 23+ dev-agent comments that were posted under my (Marko's) 
  account across EPIC 4 and 5, done manually via Jira UI. Confirmed clean.
- KAN-114 marked OBSOLETE (misdiagnosis about "dead ACF code"). Still needs 
  manual delete from Jira UI — API tool doesn't support ticket deletion.

### Key architecture reversal from earlier belief

Aja explicitly requires ACF Pro (installed from provided zip), not "100% 
native Gutenberg with no ACF" as an earlier session assumed. Confirmed via 
Local by Flywheel wp-admin — ACF Pro 6.8.0.1 is installed and active, along 
with 7 other required plugins (Yoast SEO, Loco Translate, SVG Support, 
Simple History, Query Monitor, Imagify, Virtual Robots.txt). The 
ACF-registered Gutenberg blocks in `plugins/nekoko/includes/blocks/` are 
the target architecture, not dead code.

### Verified code-level gaps I documented in docs/

1. `_booking_status` never advances from `pending` to `completed` — no code 
   path exists. Blocks review flow end-to-end. Tracked as KAN-113.
2. `Nekoko_Review_CPT::save()` publishes reviews directly with 
   `post_status='publish'` — no moderation queue despite what AC promises.
3. SMTP password blank in `nekoko-bootstrap.php` line 85 — all emails fail 
   silently.
4. Seed script `wp-content/nekoko-seed.php` references dead 
   `service_listing` CPT and `service_category` taxonomy (renamed to 
   `job`/`job_category` on 2026-06-19). Produces 0 listings if rerun.
5. Any pre-existing `service_listing` posts in the DB (from earlier seed 
   runs) are orphaned — WordPress no longer recognizes that CPT.
6. Safety modal shows on any page every 90 days for logged-in users, not 
   specifically "before first booking" as AC claims.

### PO practices I locked in this session

- AC checkboxes stay unchecked until I personally attach proof. Not before.
- Done requires proof from me, not from a dev agent.
- If a ticket says something is "verified" and I didn't personally verify 
  it, it's not verified.
- Tickets are for "what and why", not "how". Technical detail lives in 
  `docs/`.
- Never let an AI agent post comments under my name without a clear signed 
  attribution.
- When starting a new chat session, re-attach `docs/*.md` from repo if 
  relevant, and share this memory.md so continuity isn't lost.

### What I still need to do manually

- Delete KAN-114 from Jira UI (API doesn't support it)
- Verify in wp-admin whether category descriptions actually exist 
  (KAN-73 claim was made by dev agent, not personally verified)
- Verify in wp-admin whether About Us / How It Works / FAQ pages have 
  final Serbian copy or placeholder
- Check how many `service_listing` orphaned posts exist in the DB
- Fill the SMTP password in `nekoko-bootstrap.php` when ready to deploy

### Where things live now

- Technical facts (CPTs, plugin stack, flows, deployment) → 
  `docs/architecture.md`, `docs/booking-flow.md`, `docs/review-system.md`, 
  `docs/deployment.md` in the repo. Do not duplicate these here.
- Definition of Done → `docs/definition-of-done.md` in the repo.
- Team rules for AI agents → `CLAUDE.md` in the repo.
- How to write a new ticket → `Create US.md` in this folder.
- How to fix an existing ticket → `Improve US.md` in this folder.
- Skills for dev/QA/design agents → `.claude/skills/` in the repo.
- Session state and my personal running notes → this file.

### Next session should start with

Continuing the Jira rewrite: EPIC 1 (KAN-22 and its subtasks), then EPIC 2, 
EPIC 3, EPIC 6. Pull each ticket, apply the same format as EPIC 4/5. For 
EPIC 3 preserve Figma-link comments from Ivana and Aya — they are 
legitimate.
