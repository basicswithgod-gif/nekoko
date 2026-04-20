<!DOCTYPE html>
<html lang="sr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NekoKO – Neobični poslovi u Srbiji</title>
  <meta name="description" content="NekoKO portal spaja ljude sa neobičnim veštinama i poslodavce kojima trebaju upravo takvi. Prijavi se i budi prvi obavešten." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    :root {
      --blue:      #004682;
      --blue-dark: #003560;
      --yellow:    #F5B335;
      --red:       #BD433F;
      --gray-light:#CFCFCF;
      --gray-mid:  #EDEDED;
      --bg:        #FAFAF7;
      --text:      #2E2E2E;
      --text-muted:#6B6B6B;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    nav {
      background: var(--blue);
      padding: 1.1rem 2.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .logo {
      font-size: 1.4rem;
      font-weight: 600;
      color: #fff;
      letter-spacing: -0.5px;
      text-decoration: none;
    }
    .logo span { color: var(--yellow); }
    .nav-tag { font-size: 0.75rem; color: rgba(255,255,255,0.6); letter-spacing: 0.5px; }

    .hero {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 5rem 1.5rem 3rem;
    }
    .badge {
      display: inline-block;
      background: var(--yellow);
      color: var(--text);
      font-size: 0.7rem;
      font-weight: 600;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      padding: 5px 14px;
      border-radius: 20px;
      margin-bottom: 1.5rem;
    }
    h1 {
      font-size: clamp(1.9rem, 5vw, 3.2rem);
      font-weight: 600;
      line-height: 1.2;
      color: var(--blue);
      max-width: 680px;
      margin-bottom: 1rem;
    }
    h1 em { color: var(--yellow); font-style: normal; }
    .subtitle {
      font-size: 1.05rem;
      line-height: 1.75;
      color: var(--text-muted);
      max-width: 500px;
      margin-bottom: 2.5rem;
    }

    .form-wrap {
      display: flex;
      gap: 10px;
      max-width: 480px;
      width: 100%;
      flex-wrap: wrap;
      justify-content: center;
    }
    .form-wrap input[type="email"] {
      flex: 1;
      min-width: 220px;
      padding: 0.78rem 1rem;
      border: 1.5px solid var(--gray-light);
      border-radius: 8px;
      font-size: 0.95rem;
      font-family: 'Inter', sans-serif;
      background: #fff;
      color: var(--text);
      outline: none;
      transition: border-color 0.2s;
    }
    .form-wrap input[type="email"]:focus { border-color: var(--blue); }
    .form-wrap input[type="email"].error { border-color: var(--red); }

    .form-wrap button {
      background: var(--blue);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 0.78rem 1.5rem;
      font-size: 0.95rem;
      font-weight: 600;
      font-family: 'Inter', sans-serif;
      cursor: pointer;
      transition: background 0.2s, opacity 0.2s;
      white-space: nowrap;
    }
    .form-wrap button:hover { background: var(--blue-dark); }
    .form-wrap button:disabled { opacity: 0.6; cursor: not-allowed; }

    .form-note { font-size: 0.75rem; color: #999; margin-top: 0.75rem; }

    .msg {
      display: none;
      border-radius: 8px;
      padding: 0.75rem 1.25rem;
      font-size: 0.9rem;
      font-weight: 500;
      margin-top: 0.75rem;
      max-width: 480px;
      width: 100%;
      text-align: center;
    }
    .msg.success { background: #eaf4ea; color: #2a6e2a; border: 1.5px solid #b3d9b3; }
    .msg.error   { background: #fdecea; color: #7a1f1f; border: 1.5px solid #f5b3b3; }
    .msg.show    { display: block; }

    .divider { width: 56px; height: 3px; background: var(--yellow); border-radius: 2px; margin: 3rem auto 2rem; }

    .cats {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: center;
      padding: 0 1.5rem 4rem;
      max-width: 700px;
      margin: 0 auto;
    }
    .cat {
      background: #fff;
      border: 1.5px solid var(--gray-mid);
      border-radius: 10px;
      padding: 0.55rem 1rem;
      font-size: 0.82rem;
      color: var(--blue);
      font-weight: 500;
    }

    footer {
      background: var(--blue);
      color: rgba(255,255,255,0.55);
      text-align: center;
      padding: 1.2rem 1.5rem;
      font-size: 0.75rem;
    }
    footer strong { color: var(--yellow); }

    @media (max-width: 480px) {
      nav { padding: 1rem 1.25rem; }
      .hero { padding: 3.5rem 1.25rem 2rem; }
      .form-wrap input[type="email"],
      .form-wrap button { width: 100%; }
    }
  </style>
</head>
<body>

  <nav>
    <a class="logo" href="#">Neko<span>KO</span><span style="font-size:1rem;"> .</span></a>
    <span class="nav-tag">Uskoro</span>
  </nav>

  <main class="hero">
    <div class="badge">Uskoro dolazi</div>

    <h1>Neobični poslovi u Srbiji,<br>na <em>jednom mestu</em></h1>

    <p class="subtitle">
      Tražiš posao koji nije kao svi drugi? NekoKO portal spaja ljude
      sa neobičnim veštinama i poslodavce kojima trebaju upravo takvi.
    </p>

    <div class="form-wrap" id="form-wrap">
      <input type="email" id="email-input" placeholder="Tvoj email..." autocomplete="email" />
      <button id="submit-btn" type="button">Obavesti me</button>
    </div>
    <p class="form-note" id="form-note">Bez spama. Pišemo samo kad je važno.</p>
    <div class="msg" id="msg"></div>

    <div class="divider"></div>

    <div class="cats">
      <div class="cat">🎭 &nbsp;Kreativni poslovi</div>
      <div class="cat">🔧 &nbsp;Zanatske veštine</div>
      <div class="cat">🌿 &nbsp;Eko &amp; priroda</div>
      <div class="cat">🎓 &nbsp;Neformalno obrazovanje</div>
      <div class="cat">🐾 &nbsp;Briga o životinjama</div>
      <div class="cat">🎪 &nbsp;Događaji &amp; zabava</div>
      <div class="cat">🛖 &nbsp;Ruralni &amp; seoski</div>
      <div class="cat">✨ &nbsp;I još mnogo toga...</div>
    </div>
  </main>

  <footer>
    <strong>NekoKO</strong> &nbsp;·&nbsp; Prave prilike za prave ljude &nbsp;·&nbsp; &copy; 2025
  </footer>

  <script>
    const input  = document.getElementById('email-input');
    const btn    = document.getElementById('submit-btn');
    const msg    = document.getElementById('msg');
    const wrap   = document.getElementById('form-wrap');
    const note   = document.getElementById('form-note');

    function showMsg(text, type) {
      msg.textContent = text;
      msg.className = 'msg show ' + type;
    }

    function isValidEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    input.addEventListener('input', () => input.classList.remove('error'));

    btn.addEventListener('click', async () => {
      const email = input.value.trim();

      if (!isValidEmail(email)) {
        input.classList.add('error');
        input.focus();
        return;
      }

      btn.disabled = true;
      btn.textContent = 'Čekaj...';

      try {
        const res  = await fetch('subscribe.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email })
        });
        const data = await res.json();

        if (data.status === 'ok') {
          wrap.style.display = 'none';
          note.style.display = 'none';
          showMsg('✓  Prijava uspešna! Javiće se kad NekoKO krene sa radom.', 'success');
        } else if (data.status === 'duplicate') {
          showMsg('Ovaj email je već prijavljen. Hvala!', 'error');
          btn.disabled = false;
          btn.textContent = 'Obavesti me';
        } else {
          showMsg('Došlo je do greške. Pokušaj ponovo.', 'error');
          btn.disabled = false;
          btn.textContent = 'Obavesti me';
        }
      } catch (err) {
        showMsg('Greška u konekciji. Pokušaj ponovo.', 'error');
        btn.disabled = false;
        btn.textContent = 'Obavesti me';
      }
    });
  </script>

</body>
</html>
