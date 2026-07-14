# Definition of Done

Ovaj dokument važi za sve tikete u NekoKo projektu. Ne treba biti kopiran u pojedinačne tikete — jedno je mesto istine i primenjuje se automatski.

Kada se DoD menja, menja se ovaj fajl kroz pull request i tim se obaveštava u Slack-u.

---

## Osnovno pravilo

Nijedan US ili subtask ne prelazi u status **Done** dok nisu ispunjeni **svi** uslovi ispod.

---

## Univerzalni uslovi (važe za svaki tiket)

1. **Sve Acceptance Criteria iz tiketa su ispunjene.** Ne "skoro sve", ne "sve osim jedne". Sve.

2. **Dokaz ispunjenosti je priložen u komentar tiketa.** Dokaz mora biti:
   - **Konkretan** — nije "gotovo je", već artefakt koji to potvrđuje
   - **Verifikabilan** — neko drugi može da otvori link, vidi screenshot, ili reprodukuje ishod
   - **Datiran** — komentar u Jiri sa jasnim datumom

3. **Uloga koja je radila na tiketu je verifikovala završetak.** Developer ne zatvara dev tiket sa "izgleda da radi". Testira i potvrđuje.

4. **Slack update poslat u `#all-business-english`** — kratka rečenica šta je završeno + link na tiket.

5. **Tiket zatvara osoba koja je radila na njemu.** Ne PO, ne SM (osim ako su oni radili tiket).

---

## Dodatni uslovi po tipu tiketa

### Dev tiket

- Kod merge-ovan u `develop` branch
- CI je prošao (svi testovi zeleni)
- Ako je novi funkcionalni deo — smoke test odrađen na `nekoko.local`
- Nema regresije na postojećim tokovima (registracija, oglašavanje, rezervacija, recenzija)

**Tip dokaza:** video kratkog end-to-end toka, screenshot funkcionalnosti, link ka PR-u, link ka live URL-u na `nekoko.local`.

### Dizajn tiket

- Finalni frame u Figmi je publikovan (ne u "draft" folderu)
- PO je odobrio dizajn u komentaru Figma frame-a ili u Jiri komentaru
- Design tokeni (boje, tipografija, spacing) usklađeni sa brand style guide-om
- Ako je UI — svi interaktivni stanja (hover, focus, active, error, disabled) dokumentovana

**Tip dokaza:** Figma link ka finalnom frame-u, screenshot pre/posle ako je redesign, link ka relevantnoj sekciji design system-a.

### Marketing / Copy tiket

- PO je odobrio tekst pre publikacije
- Tekst je publikovan na tačno pravu platformu i, ako je relevantno, u pravom terminu (kalendar objave)
- Ako je post o NekoKo pre lansiranja — ime platforme, Ajin identitet, tech stack detalji **nisu** otkriveni (per pre-launch content lock pravilo)

**Tip dokaza:** link ka objavljenom tekstu, screenshot posta, ako je moguće rana metrika (impresije, engagement, klikovi).

### Content / data tiket (npr. seed baze, kategorije, opisi)

- Sadržaj unesen kroz wp-admin (ili programski, ako je autor developer)
- Sadržaj vidljiv na live sajtu (`nekoko.local` ili produkcija, u zavisnosti od tiketa)
- Ako je masovni unos — poznata brojka (npr. "30 profila kreirano, potvrđeno preko WP-CLI count")

**Tip dokaza:** screenshot admin panela sa unesenim sadržajem, brojka, CSV export, ili link ka javnoj stranici gde se sadržaj vidi.

### QA tiket

- Test scenario izvršen po planu
- Rezultati (pass/fail) dokumentovani
- Ako je nešto palo — bug tiket kreiran i linkovan iz QA tiketa

**Tip dokaza:** test report, video test sesije, ili strukturirani komentar sa listom scenarija i rezultatima.

---

## Šta NIJE dokaz

Da bi izbegli konfuziju, ove stvari **nisu dovoljne** kao dokaz:

- "Gotovo je" bez ničega ispod
- "Testirao sam, radi" bez videa/screenshot-a
- Link ka commit-u bez opisa šta commit stvarno menja
- Screenshot Figma-e u "draft" folderu
- "Post je zakazan" pre nego što je stvarno objavljen

---

## Ako se DoD ne ispuni

Tiket se **ne zatvara**. Ostaje u statusu "In Progress" ili "In Review" dok gap ne bude popunjen.

Ako se ponavlja da tiketi bivaju zatvoreni bez DoD-a, to je tema za retro, ne za dodavanje podsetnika u svaki tiket.
