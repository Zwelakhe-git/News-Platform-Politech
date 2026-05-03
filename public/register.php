<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Регистрация — Politech News</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --accent: #7c6ff7;
      --accent2: #a78bfa;
      --danger: #f87171;
      --success: #34d399;
      --bg: #080b14;
      --surface: rgba(255,255,255,0.04);
      --border: rgba(255,255,255,0.08);
      --text: #f1f3f9;
      --muted: #6b7280;
      --input-bg: rgba(255,255,255,0.05);
    }

    html, body { height: 100%; }

    body {
      font-family: 'Inter', -apple-system, sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      overflow: hidden;
    }

    /* ── Animated background ── */
    .bg-glow {
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 0;
      overflow: hidden;
    }
    .bg-glow::before {
      content: '';
      position: absolute;
      top: -20%;
      left: -10%;
      width: 60%;
      height: 60%;
      background: radial-gradient(ellipse, rgba(124,111,247,0.18) 0%, transparent 70%);
      animation: drift1 12s ease-in-out infinite alternate;
    }
    .bg-glow::after {
      content: '';
      position: absolute;
      bottom: -10%;
      right: -5%;
      width: 50%;
      height: 50%;
      background: radial-gradient(ellipse, rgba(167,139,250,0.12) 0%, transparent 70%);
      animation: drift2 14s ease-in-out infinite alternate;
    }
    @keyframes drift1 { from { transform: translate(0,0) scale(1); } to { transform: translate(40px,30px) scale(1.1); } }
    @keyframes drift2 { from { transform: translate(0,0) scale(1); } to { transform: translate(-30px,-40px) scale(1.08); } }

    /* ── Layout ── */
    .layout {
      position: relative;
      z-index: 1;
      display: flex;
      width: 100%;
      min-height: 100vh;
    }

    /* ── Left panel ── */
    .panel-left {
      display: none;
      flex-direction: column;
      justify-content: space-between;
      width: 420px;
      flex-shrink: 0;
      padding: 48px 44px;
      border-right: 1px solid var(--border);
      background: rgba(255,255,255,0.02);
      backdrop-filter: blur(20px);
    }

    @media (min-width: 900px) { .panel-left { display: flex; } }

    .brand {
      font-size: 22px;
      font-weight: 800;
      letter-spacing: -0.5px;
      color: #fff;
    }
    .brand em {
      font-style: normal;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .panel-tagline {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .panel-tagline h2 {
      font-size: 36px;
      font-weight: 800;
      line-height: 1.15;
      letter-spacing: -1px;
      color: #fff;
      margin-bottom: 16px;
    }
    .panel-tagline h2 span {
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .panel-tagline p {
      font-size: 15px;
      color: var(--muted);
      line-height: 1.6;
    }

    .perks { list-style: none; margin-top: 32px; display: flex; flex-direction: column; gap: 14px; }
    .perks li {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 14px;
      color: #9ca3af;
    }
    .perk-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      background: rgba(124,111,247,0.15);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      flex-shrink: 0;
    }

    .panel-footer { font-size: 12px; color: #374151; }

    /* ── Right panel (form) ── */
    .panel-right {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 20px;
      overflow-y: auto;
    }

    .form-box {
      width: 100%;
      max-width: 440px;
    }

    /* Mobile brand */
    .mobile-brand {
      font-size: 20px;
      font-weight: 800;
      letter-spacing: -0.5px;
      color: #fff;
      margin-bottom: 28px;
      text-align: center;
    }
    .mobile-brand em {
      font-style: normal;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    @media (min-width: 900px) { .mobile-brand { display: none; } .form-heading{ margin-top: 200px} }

    .form-heading { margin-bottom: 28px; }
    .form-heading h1 { font-size: 26px; font-weight: 800; letter-spacing: -0.5px; color: #fff; margin-bottom: 6px; }
    .form-heading p { font-size: 14px; color: var(--muted); }

    /* ── Field ── */
    .field { margin-bottom: 16px; }

    .field label {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      color: #9ca3af;
      margin-bottom: 7px;
    }
    .req { color: var(--accent2); }

    .input-wrap { position: relative; }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="url"] {
      width: 100%;
      background: var(--input-bg);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 13px 16px;
      font-size: 15px;
      font-family: inherit;
      color: var(--text);
      outline: none;
      transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    }
    input[type="text"]::placeholder,
    input[type="email"]::placeholder,
    input[type="password"]::placeholder,
    input[type="url"]::placeholder { color: #374151; }

    input:focus {
      border-color: var(--accent);
      background: rgba(124,111,247,0.06);
      box-shadow: 0 0 0 3px rgba(124,111,247,0.15);
    }
    input.err { border-color: var(--danger); box-shadow: 0 0 0 3px rgba(248,113,113,0.12); }

    .eye-btn {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--muted);
      cursor: pointer;
      padding: 0;
      font-size: 16px;
      line-height: 1;
      display: flex;
      align-items: center;
    }
    .eye-btn svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 1.8; }

    .field-err {
      font-size: 12px;
      color: var(--danger);
      margin-top: 5px;
      display: none;
      align-items: center;
      gap: 4px;
    }
    .field-err.show { display: flex; }

    /* ── Strength bar ── */
    .strength-track {
      height: 3px;
      border-radius: 4px;
      background: rgba(255,255,255,0.07);
      margin-top: 8px;
      overflow: hidden;
    }
    .strength-fill {
      height: 100%;
      width: 0;
      border-radius: 4px;
      transition: width 0.35s, background 0.35s;
    }
    .strength-text {
      font-size: 11px;
      margin-top: 4px;
      font-weight: 500;
      color: var(--muted);
      transition: color 0.3s;
    }

    /* ── Role cards ── */
    .role-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }
    .role-card {
      position: relative;
      cursor: pointer;
    }
    .role-card input { position: absolute; opacity: 0; width: 0; height: 0; }
    .role-card-inner {
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 14px 12px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      background: var(--input-bg);
      transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
      text-align: center;
    }
    .role-card-inner:hover { border-color: rgba(124,111,247,0.4); }
    .role-card input:checked ~ .role-card-inner {
      border-color: var(--accent);
      background: rgba(124,111,247,0.1);
      box-shadow: 0 0 0 3px rgba(124,111,247,0.12);
    }
    .role-card-inner .r-icon { font-size: 22px; }
    .role-card-inner .r-name { font-size: 13px; font-weight: 600; color: #d1d5db; }
    .role-card-inner .r-desc { font-size: 11px; color: var(--muted); line-height: 1.3; }
    .role-card input:checked ~ .role-card-inner .r-name { color: var(--accent2); }

    /* ── Avatar preview ── */
    .avatar-row {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-top: 8px;
    }
    .avatar-ring {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      border: 2px solid var(--border);
      overflow: hidden;
      display: none;
      flex-shrink: 0;
      background: rgba(255,255,255,0.05);
    }
    .avatar-ring img { width: 100%; height: 100%; object-fit: cover; }

    /* ── Checkbox ── */
    .check-row {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin: 18px 0 4px;
    }
    .custom-check {
      width: 18px;
      height: 18px;
      border-radius: 5px;
      border: 1px solid var(--border);
      background: var(--input-bg);
      flex-shrink: 0;
      margin-top: 1px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: border-color 0.2s, background 0.2s;
    }
    .custom-check.checked {
      background: var(--accent);
      border-color: var(--accent);
    }
    .custom-check svg { width: 11px; height: 11px; display: none; }
    .custom-check.checked svg { display: block; }
    #agree_terms { display: none; }
    .check-text {
      font-size: 13px;
      color: var(--muted);
      line-height: 1.5;
      cursor: pointer;
    }
    .check-text a { color: var(--accent2); text-decoration: none; }
    .check-text a:hover { text-decoration: underline; }

    /* ── Submit btn ── */
    .btn-submit {
      width: 100%;
      margin-top: 20px;
      padding: 14px;
      border: none;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff;
      font-size: 15px;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      transition: opacity 0.2s, transform 0.15s;
      box-shadow: 0 4px 24px rgba(124,111,247,0.35);
    }
    .btn-submit:hover { opacity: 0.92; }
    .btn-submit:active { transform: scale(0.985); }
    .btn-submit:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
    .btn-submit::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, transparent 60%);
    }
    .btn-text { position: relative; z-index: 1; display: flex; align-items: center; justify-content: center; gap: 8px; }

    .login-link {
      text-align: center;
      font-size: 13px;
      color: var(--muted);
      margin-top: 20px;
    }
    .login-link a { color: var(--accent2); font-weight: 600; text-decoration: none; }
    .login-link a:hover { text-decoration: underline; }

    /* ── Success banner ── */
    .response-banner, .alert {
      display: auto;
      background: #e6f4ea;
      border-radius: 8px;
      padding: 1rem;
      font-size: .9rem;
      margin-bottom: 1.2rem;
      text-align: center;
      position: fixed;
      top: 10px;
      right: 10px;
      transition: transform 0.3s linear;
      transform: translate(150%, 0px);
    }

    .response-banner.success{
      border: 1px solid #34a853;
      color: #137333;
    }
    .response-banner.success::after{
      content: '';
    }
    .response-banner.fail{
      border: 1px solid #a83434ff;
      color: #731313ff;
    }
    .response-banner.visible,.alert.visible { display: flex; transform: translate(0%, 0%); }

    /* ── Divider ── */
    .divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 4px 0 16px;
    }
    .divider-line { flex: 1; height: 1px; background: var(--border); }
    .divider-text { font-size: 11px; color: #374151; font-weight: 500; letter-spacing: 0.05em; text-transform: uppercase; }
  </style>
</head>
<body>

<div class="bg-glow"></div>

<div class="layout">

  <!-- ── Left panel ── -->
  <aside class="panel-left">
    <div class="brand">spb<em>Vkurse</em></div>

    <div class="panel-tagline">
      <h2>Читайте.<br><span>Пишите.</span><br>Будьте в курсе.</h2>
      <p>Присоединяйтесь к платформе, где авторы создают качественный контент, а читатели получают самые свежие новости.</p>
      <ul class="perks">
        <li>
          <div class="perk-icon"><i class="fa-solid fa-newspaper"></i></div>
          Новости в реальном времени из проверенных источников
        </li>
        <li>
          <div class="perk-icon"><i class="fa-solid fa-pen-clip"></i></div>
          Публикуйте собственные материалы как автор
        </li>
        <li>
          <div class="perk-icon"><i class="fa-solid fa-bell"></i></div>
          Персональные уведомления и подписки на авторов
        </li>
        <li>
          <div class="perk-icon"><i class="fa-solid fa-shield"></i></div>
          Безопасная платформа с модерацией контента
        </li>
      </ul>
    </div>

    <div class="panel-footer">© 2026 PolitechNews. Все права защищены.</div>
  </aside>

  <!-- ── Right panel ── -->
  <main class="panel-right">
    <div class="form-box">

      <div class="mobile-brand">spb<em>Vkurse</em></div>

      <div class="form-heading">
        
      </div>

      <div class="response-banner alert" id="responseBanner">
        
      </div>

      <form id="registerForm" novalidate>

        <!-- Full Name -->
        <div class="field">
          <label>Полное имя <span class="req">*</span></label>
          <input type="text" id="full_name" placeholder="Иван Иванов" maxlength="100" autocomplete="name" />
          <div class="field-err" id="err_full_name">Введите ваше полное имя</div>
        </div>

        <!-- Email -->
        <div class="field">
          <label>Email <span class="req">*</span></label>
          <input type="email" id="email" placeholder="ivan@example.com" maxlength="255" autocomplete="email" />
          <div class="field-err" id="err_email">Введите корректный email-адрес</div>
        </div>

        <!-- Password -->
        <div class="field">
          <label>Пароль <span class="req">*</span></label>
          <div class="input-wrap">
            <input type="password" id="password" placeholder="Минимум 8 символов" autocomplete="new-password" style="padding-right:44px" />
            <button type="button" class="eye-btn" onclick="togglePw('password',this)" aria-label="Показать пароль">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <div class="strength-track"><div class="strength-fill" id="strengthFill"></div></div>
          <div class="strength-text" id="strengthText"></div>
          <div class="field-err" id="err_password">Минимум 8 символов</div>
        </div>

        <!-- Confirm Password -->
        <div class="field">
          <label>Повторите пароль <span class="req">*</span></label>
          <div class="input-wrap">
            <input type="password" id="password_confirm" placeholder="Ещё раз" autocomplete="new-password" style="padding-right:44px" />
            <button type="button" class="eye-btn" onclick="togglePw('password_confirm',this)" aria-label="Показать пароль">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <div class="field-err" id="err_password_confirm">Пароли не совпадают</div>
        </div>

        <div class="divider">
          <div class="divider-line"></div>
          <div class="divider-text">Настройки профиля</div>
          <div class="divider-line"></div>
        </div>

        <!-- Role -->
        <div class="field role-field">
          <label>Роль <span class="req">*</span></label>
          <div class="role-grid">
            <label class="role-card">
              <input type="radio" name="role" value="reader" checked />
              <div class="role-card-inner">
                <div class="r-icon"><span class="icon"><i class="fa-solid fa-user"></i></span></div>
                <div class="r-name">Читатель</div>
                <div class="r-desc">Читайте и комментируйте</div>
              </div>
            </label>
            <label class="role-card">
              <input type="radio" name="role" value="author" />
              <div class="role-card-inner">
                <div class="r-icon"><i class="fa-solid fa-pen-clip"></i></div>
                <div class="r-name">Автор</div>
                <div class="r-desc">Публикуйте материалы</div>
              </div>
            </label>
          </div>
        </div>

        <!-- Avatar URL -->
        <div class="field" style="display: none">
          <label>Аватар <span style="color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0">(необязательно)</span></label>
          <input type="url" id="avatar_url" placeholder="https://example.com/photo.png" />
          <div class="avatar-row">
            <div class="avatar-ring" id="avatarRing">
              <img id="avatarPreview" class="avatarImg" src="" alt="preview" />
            </div>
          </div>
          <div class="field-err" id="err_avatar_url">Введите корректный URL</div>
        </div>

        <!-- Terms -->
        <div class="check-row" onclick="toggleCheck()">
          <div class="custom-check" id="checkBox">
            <svg viewBox="0 0 12 12" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="2,6 5,9 10,3"/>
            </svg>
          </div>
          <input type="checkbox" id="agree_terms" />
          <span class="check-text">
            Принимаю <a href="#" onclick="event.stopPropagation()">Пользовательское соглашение</a> и
            <a href="#" onclick="event.stopPropagation()">Политику конфиденциальности</a>
          </span>
        </div>
        <div class="field-err" id="err_terms" style="margin-left:28px">Необходимо принять условия</div>

        <button type="submit" class="btn-submit" id="submitBtn">
          <span class="btn-text">Создать аккаунт →</span>
        </button>

      </form>

      <p class="login-link"><span class="login-link-text">Уже есть аккаунт?</span> <a class="form-type-toggler">Войти</a></p>

    </div>
  </main>
</div>

<script src="/vkurse/static/js/register.js">
</script>
</body>
</html>
