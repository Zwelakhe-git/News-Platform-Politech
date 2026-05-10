<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>API Ключи — spbVkurse</title>
  <style>
    :root {
      --bg: #0d0f14;
      --surface: #151820;
      --surface2: #1c2030;
      --border: rgba(255,255,255,.07);
      --accent: #7c6af7;
      --accent2: #a78bfa;
      --text: #e8eaf0;
      --muted: #6b7280;
      --green: #34d399;
      --red: #f87171;
      --yellow: #fbbf24;
      --cyan: #22d3ee;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', -apple-system, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

    /* ── Navbar ── */
    .navbar {
      position: sticky; top: 0; z-index: 100;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 32px; height: 60px;
      background: rgba(13,15,20,.9); backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border);
    }
    .navbar__logo { font-size: 1.15rem; font-weight: 800; letter-spacing: -.4px; }
    .navbar__logo span { color: var(--accent2); }
    .navbar__right { display: flex; align-items: center; gap: 10px; }
    .nav-link {
      font-size: .8rem; color: var(--muted); text-decoration: none;
      padding: 6px 14px; border: 1px solid var(--border); border-radius: 8px;
      transition: border-color .15s, color .15s;
    }
    .nav-link:hover { color: var(--text); border-color: var(--accent); }
    .avatar-sm {
      width: 36px; height: 36px; border-radius: 50%;
      background: linear-gradient(135deg, var(--accent), #ec4899);
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: .85rem; border: 2px solid var(--border);
    }

    /* ── Layout ── */
    .main { max-width: 860px; margin: 0 auto; padding: 36px 24px 80px; }

    .page-header { margin-bottom: 32px; }
    .page-header h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: 6px; }
    .page-header p { font-size: .88rem; color: var(--muted); line-height: 1.6; }

    /* ── Access denied ── */
    .access-denied {
      display: none;
      background: rgba(248,113,113,.07); border: 1px solid rgba(248,113,113,.2);
      border-radius: 16px; padding: 40px; text-align: center;
    }
    .access-denied.visible { display: block; }
    .access-denied__icon { font-size: 3rem; margin-bottom: 16px; }
    .access-denied h3 { font-size: 1.1rem; font-weight: 700; color: var(--red); margin-bottom: 8px; }
    .access-denied p { font-size: .85rem; color: var(--muted); }

    /* ── Info banner ── */
    .info-banner {
      background: rgba(124,106,247,.08); border: 1px solid rgba(124,106,247,.2);
      border-radius: 14px; padding: 16px 20px; margin-bottom: 24px;
      display: flex; gap: 14px; align-items: flex-start;
    }
    .info-banner__icon { font-size: 1.2rem; flex-shrink: 0; margin-top: 1px; }
    .info-banner__text { font-size: .83rem; color: var(--muted); line-height: 1.6; }
    .info-banner__text strong { color: var(--accent2); }

    /* ── Create form card ── */
    .card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 16px; padding: 24px 28px; margin-bottom: 20px;
    }
    .card__title {
      font-size: .7rem; font-weight: 700; letter-spacing: 1.2px;
      text-transform: uppercase; color: var(--muted); margin-bottom: 20px;
    }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-group { margin-bottom: 16px; }
    label {
      display: block; font-size: .78rem; font-weight: 600;
      color: var(--muted); margin-bottom: 6px; letter-spacing: .3px; text-transform: uppercase;
    }
    input[type="text"], select {
      width: 100%; padding: 10px 14px;
      background: var(--surface2); color: var(--text);
      border: 1px solid var(--border); border-radius: 10px;
      font-size: .88rem; outline: none; font-family: inherit;
      transition: border-color .15s, box-shadow .15s;
      appearance: none;
    }
    input:focus, select:focus {
      border-color: var(--accent); box-shadow: 0 0 0 3px rgba(124,106,247,.15);
    }
    .input-hint { font-size: .72rem; color: var(--muted); margin-top: 5px; }
    .error-msg { font-size: .72rem; color: var(--red); margin-top: 5px; display: none; }
    .error-msg.visible { display: block; }

    /* Scopes checkboxes */
    .scopes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px; margin-top: 4px; }
    .scope-item {
      display: flex; align-items: center; gap: 8px;
      padding: 9px 12px; background: var(--surface2);
      border: 1px solid var(--border); border-radius: 10px; cursor: pointer;
      transition: border-color .15s;
    }
    .scope-item:has(input:checked) { border-color: rgba(124,106,247,.4); background: rgba(124,106,247,.07); }
    .scope-item input[type="checkbox"] { accent-color: var(--accent); width: 14px; height: 14px; cursor: pointer; }
    .scope-item__label { font-size: .8rem; font-weight: 500; }
    .scope-item__desc { font-size: .7rem; color: var(--muted); }

    /* Expiry options */
    .expiry-options { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
    .expiry-btn {
      padding: 7px 16px; border-radius: 20px; border: 1px solid var(--border);
      background: var(--surface2); color: var(--muted); font-size: .8rem;
      cursor: pointer; transition: all .15s;
    }
    .expiry-btn.active { background: rgba(124,106,247,.15); border-color: var(--accent); color: var(--accent2); font-weight: 600; }

    /* ── Buttons ── */
    .btn-primary {
      padding: 10px 24px; background: var(--accent); color: #fff;
      border: none; border-radius: 10px; font-size: .88rem; font-weight: 600;
      cursor: pointer; transition: background .15s, transform .1s;
    }
    .btn-primary:hover { background: #6a59e0; }
    .btn-primary:active { transform: scale(.98); }
    .btn-ghost {
      padding: 10px 20px; background: transparent; color: var(--text);
      border: 1px solid var(--border); border-radius: 10px; font-size: .88rem;
      cursor: pointer; transition: background .15s, border-color .15s;
    }
    .btn-ghost:hover { background: var(--surface2); border-color: var(--accent); }

    /* ── New key reveal modal ── */
    .modal-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,.7);
      display: flex; align-items: center; justify-content: center;
      z-index: 200; backdrop-filter: blur(4px); display: none;
    }
    .modal-overlay.visible { display: flex; }
    .modal {
      background: var(--surface); border: 1px solid rgba(52,211,153,.3);
      border-radius: 20px; padding: 32px; max-width: 520px; width: 90%; animation: popIn .2s ease;
    }
    @keyframes popIn { from { transform: scale(.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal__icon { font-size: 2.5rem; text-align: center; margin-bottom: 16px; }
    .modal h3 { font-size: 1.1rem; font-weight: 700; color: var(--green); margin-bottom: 8px; text-align: center; }
    .modal p { font-size: .83rem; color: var(--muted); text-align: center; margin-bottom: 20px; line-height: 1.6; }
    .key-display {
      background: var(--surface2); border: 1px solid rgba(52,211,153,.2);
      border-radius: 12px; padding: 16px; font-family: monospace; font-size: .88rem;
      color: var(--green); word-break: break-all; margin-bottom: 16px; position: relative;
    }
    .copy-btn {
      position: absolute; top: 10px; right: 10px;
      background: rgba(52,211,153,.15); border: 1px solid rgba(52,211,153,.3);
      color: var(--green); border-radius: 6px; padding: 4px 10px; font-size: .72rem;
      cursor: pointer; transition: background .15s;
    }
    .copy-btn:hover { background: rgba(52,211,153,.25); }
    .modal__warning {
      background: rgba(251,191,36,.08); border: 1px solid rgba(251,191,36,.2);
      border-radius: 10px; padding: 12px 16px; font-size: .78rem; color: var(--yellow);
      margin-bottom: 20px; line-height: 1.5;
    }

    /* ── Keys list ── */
    .keys-list { display: flex; flex-direction: column; gap: 12px; }
    .key-row {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 14px; padding: 18px 22px;
      display: flex; align-items: center; gap: 16px;
    }
    .key-row.revoked { opacity: .5; }
    .key-row__icon {
      width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
      background: rgba(124,106,247,.15); display: flex; align-items: center;
      justify-content: center; font-size: 1.1rem;
    }
    .key-row__body { flex: 1; min-width: 0; }
    .key-row__name { font-size: .9rem; font-weight: 600; margin-bottom: 4px; }
    .key-row__key {
      font-family: monospace; font-size: .78rem; color: var(--muted);
      background: var(--surface2); padding: 3px 8px; border-radius: 6px;
      display: inline-block; letter-spacing: .5px;
    }
    .key-row__meta { font-size: .72rem; color: var(--muted); margin-top: 6px; display: flex; gap: 14px; flex-wrap: wrap; }
    .key-row__scopes { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 6px; }
    .scope-tag {
      padding: 2px 8px; border-radius: 4px; font-size: .68rem; font-weight: 600;
      letter-spacing: .3px; text-transform: uppercase;
      background: rgba(124,106,247,.12); color: var(--accent2);
      border: 1px solid rgba(124,106,247,.2);
    }
    .scope-tag--read { background: rgba(34,211,238,.1); color: var(--cyan); border-color: rgba(34,211,238,.2); }
    .scope-tag--write { background: rgba(251,191,36,.1); color: var(--yellow); border-color: rgba(251,191,36,.2); }
    .scope-tag--admin { background: rgba(248,113,113,.1); color: var(--red); border-color: rgba(248,113,113,.2); }

    .key-row__right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
    .status-dot {
      display: flex; align-items: center; gap: 5px; font-size: .73rem;
    }
    .dot { width: 7px; height: 7px; border-radius: 50%; }
    .dot--active { background: var(--green); box-shadow: 0 0 6px var(--green); }
    .dot--revoked { background: var(--muted); }
    .dot--expired { background: var(--red); }
    .btn-revoke {
      padding: 5px 14px; border-radius: 8px; font-size: .75rem; font-weight: 600;
      background: rgba(248,113,113,.1); color: var(--red);
      border: 1px solid rgba(248,113,113,.25); cursor: pointer; transition: background .15s;
    }
    .btn-revoke:hover { background: rgba(248,113,113,.2); }
    .btn-revoke:disabled { opacity: .4; cursor: not-allowed; }

    /* ── Demo bar ── */
    .demo-bar {
      background: rgba(124,106,247,.08); border-bottom: 1px solid rgba(124,106,247,.2);
      padding: 10px 32px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
      font-size: .78rem; color: var(--muted);
    }
    .demo-bar strong { color: var(--accent2); }
    .demo-btn {
      padding: 4px 12px; border-radius: 20px; border: 1px solid var(--border);
      background: var(--surface); color: var(--text); font-size: .78rem; cursor: pointer;
      transition: background .15s, border-color .15s;
    }
    .demo-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }
    .demo-btn:hover:not(.active) { border-color: var(--accent); }

    /* ── Toast ── */
    .toast {
      position: fixed; bottom: 24px; right: 24px;
      background: var(--surface); border: 1px solid rgba(52,211,153,.3);
      color: var(--green); padding: 12px 20px; border-radius: 12px;
      font-size: .85rem; font-weight: 600; display: none;
      box-shadow: 0 8px 32px rgba(0,0,0,.4);
    }
    .toast.visible { display: flex; align-items: center; gap: 8px; animation: slideIn .3s ease; }
    @keyframes slideIn { from { transform: translateY(16px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    .empty-state { text-align: center; padding: 48px 24px; color: var(--muted); }
    .empty-state__icon { font-size: 3rem; margin-bottom: 12px; }
    .empty-state p { font-size: .85rem; }

    @media(max-width:640px) {
      .form-row { grid-template-columns: 1fr; }
      .key-row { flex-direction: column; align-items: flex-start; }
      .key-row__right { flex-direction: row; align-items: center; width: 100%; justify-content: space-between; }
      .navbar { padding: 0 16px; }
      .main { padding: 20px 16px 60px; }
      .demo-bar { padding: 10px 16px; }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="navbar__logo">Politech<span>News</span></div>
  <div class="navbar__right">
    <a href="settings.html" class="nav-link">⚙ Настройки</a>
    <a href="profile.html" class="nav-link">← Профиль</a>
    <div class="avatar-sm">М</div>
  </div>
</nav>

<!-- Demo role switcher -->
<div class="demo-bar">
  <strong>Demo:</strong> <span>Роль:</span>
  <button class="demo-btn" onclick="setRole('reader')">Читатель</button>
  <button class="demo-btn" onclick="setRole('author')">Автор</button>
  <button class="demo-btn" onclick="setRole('moderator')">Модератор</button>
  <button class="demo-btn active" onclick="setRole('admin')">Администратор</button>
</div>

<main class="main">

  <div class="page-header">
    <h1>🔑 API Ключи</h1>
    <p>Создавайте и управляйте ключами для доступа к Politech News API.<br>
       Только авторизованные пользователи могут вносить изменения через API.</p>
  </div>

  <!-- Access denied for readers -->
  <div class="access-denied" id="accessDenied">
    <div class="access-denied__icon">🚫</div>
    <h3>Доступ запрещён</h3>
    <p>API ключи доступны только для пользователей с ролью <strong>author</strong>, <strong>moderator</strong> или <strong>admin</strong>.<br>
    Обратитесь к администратору для получения расширенных прав.</p>
  </div>

  <!-- Main content for authorized users -->
  <div id="mainContent">

    <!-- Info banner -->
    <div class="info-banner">
      <div class="info-banner__icon">ℹ️</div>
      <div class="info-banner__text">
        Ключи используются для аутентификации запросов к API.
        Формат: <strong>vkurse_[random]</strong> — передавайте в заголовке
        <strong>Authorization: Bearer &lt;key&gt;</strong>.
        Пример эндпоинта: <strong style="color:var(--cyan);font-family:monospace">GET /vkurse/api/v1/news/{category}/{key}/{id}</strong>
      </div>
    </div>

    <!-- Create new key -->
    <div class="card">
      <div class="card__title">Создать новый ключ · api_keys</div>

      <!-- name -->
      <div class="form-group">
        <label for="key_name">Название ключа <span style="color:var(--red)">*</span></label>
        <input type="text" id="key_name" name="key_name"
               placeholder="Например: Production, Mobile App, Testing..."
               maxlength="100" />
        <div class="input-hint">api_keys.name · VARCHAR(100) — только для вашего удобства</div>
        <div class="error-msg" id="err_key_name">Введите название ключа</div>
      </div>

      <!-- permissions / scopes -->
      <div class="form-group">
        <label>Разрешения (scopes) <span style="color:var(--red)">*</span></label>
        <div class="scopes-grid">
          <label class="scope-item">
            <input type="checkbox" name="scope" value="news:read" checked />
            <div>
              <div class="scope-item__label">news:read</div>
              <div class="scope-item__desc">Чтение новостей</div>
            </div>
          </label>
          <label class="scope-item">
            <input type="checkbox" name="scope" value="news:write" />
            <div>
              <div class="scope-item__label">news:write</div>
              <div class="scope-item__desc">Создание / редактирование</div>
            </div>
          </label>
          <label class="scope-item">
            <input type="checkbox" name="scope" value="comments:read" />
            <div>
              <div class="scope-item__label">comments:read</div>
              <div class="scope-item__desc">Чтение комментариев</div>
            </div>
          </label>
          <label class="scope-item">
            <input type="checkbox" name="scope" value="comments:write" />
            <div>
              <div class="scope-item__label">comments:write</div>
              <div class="scope-item__desc">Публикация комментариев</div>
            </div>
          </label>
          <label class="scope-item" id="scopeAdminItem" style="display:none">
            <input type="checkbox" name="scope" value="admin" />
            <div>
              <div class="scope-item__label">admin</div>
              <div class="scope-item__desc">Полный доступ</div>
            </div>
          </label>
        </div>
        <div class="error-msg" id="err_scopes">Выберите хотя бы одно разрешение</div>
      </div>

      <!-- expires_at -->
      <div class="form-group">
        <label>Срок действия · api_keys.expires_at</label>
        <div class="expiry-options">
          <button type="button" class="expiry-btn" onclick="setExpiry(this,'30d')">30 дней</button>
          <button type="button" class="expiry-btn active" onclick="setExpiry(this,'90d')">90 дней</button>
          <button type="button" class="expiry-btn" onclick="setExpiry(this,'1y')">1 год</button>
          <button type="button" class="expiry-btn" onclick="setExpiry(this,'never')">Без ограничений</button>
        </div>
        <div class="input-hint">api_keys.expires_at · TIMESTAMP [NULL = без срока]</div>
      </div>

      <div style="display:flex;gap:10px;margin-top:4px">
        <button class="btn-primary" onclick="createKey()">+ Создать ключ</button>
        <button class="btn-ghost" onclick="resetForm()">Сбросить</button>
      </div>
    </div>

    <!-- Keys list -->
    <div class="card">
      <div class="card__title">Активные ключи</div>
      <div class="keys-list" id="keysList">
        <!-- populated by JS -->
      </div>
      <div class="empty-state" id="emptyState" style="display:none">
        <div class="empty-state__icon">🗝️</div>
        <p>У вас пока нет API ключей.<br>Создайте первый ключ выше.</p>
      </div>
    </div>

  </div><!-- /mainContent -->
</main>

<!-- New key modal -->
<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal__icon">🎉</div>
    <h3>Ключ успешно создан!</h3>
    <p>Скопируйте ключ сейчас — он будет показан только один раз.<br>
       В системе хранится только его хэш.</p>
    <div class="key-display" id="modalKeyValue">
      vkurse_xxxxxxxxxxxxxxxxxxxxxxxx
      <button class="copy-btn" onclick="copyKey()">Копировать</button>
    </div>
    <div class="modal__warning">
      ⚠️ После закрытия этого окна ключ нельзя будет просмотреть повторно.
      Храните его в безопасном месте — это соответствует полю <strong>api_keys.key_hash</strong> (хэшируется на сервере).
    </div>
    <button class="btn-primary" style="width:100%" onclick="closeModal()">Понял, закрыть</button>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">✅ <span id="toastMsg"></span></div>

<script>
  /* ── State ── */
  let currentRole = 'admin';
  let selectedExpiry = '90d';

  const ROLES_ALLOWED = ['author', 'moderator', 'admin'];

  /* Sample existing keys */
  let keys = [
    {
      id: '1', name: 'Production', prefix: 'vkurse_69cf7c65d5dbd5',
      scopes: ['news:read', 'news:write'],
      status: 'active', created_at: '2026-01-15', last_used_at: '2026-04-03', expires_at: '2026-07-15'
    },
    {
      id: '2', name: 'Read-only Mobile',
      prefix: 'vkurse_3ab812fe209d11',
      scopes: ['news:read', 'comments:read'],
      status: 'active', created_at: '2026-02-20', last_used_at: '2026-04-01', expires_at: null
    },
    {
      id: '3', name: 'Old Testing Key',
      prefix: 'vkurse_c4d902ef71aa88',
      scopes: ['news:read'],
      status: 'revoked', created_at: '2025-11-01', last_used_at: '2026-01-10', expires_at: '2026-05-01'
    },
  ];

  /* ── Role switching ── */
  function setRole(role) {
    currentRole = role;
    document.querySelectorAll('.demo-btn').forEach(b => b.classList.toggle('active', b.textContent.toLowerCase().includes(role.substring(0,4))));
    const allowed = ROLES_ALLOWED.includes(role);
    document.getElementById('accessDenied').classList.toggle('visible', !allowed);
    document.getElementById('mainContent').style.display = allowed ? 'block' : 'none';
    // admin scope only for admin/moderator
    document.getElementById('scopeAdminItem').style.display = (role === 'admin' || role === 'moderator') ? 'flex' : 'none';
  }

  /* ── Expiry ── */
  function setExpiry(btn, val) {
    selectedExpiry = val;
    document.querySelectorAll('.expiry-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }

  /* ── Render keys ── */
  function renderKeys() {
    const list = document.getElementById('keysList');
    const empty = document.getElementById('emptyState');
    if (!keys.length) { list.innerHTML = ''; empty.style.display = 'block'; return; }
    empty.style.display = 'none';
    list.innerHTML = keys.map(k => {
      const scopeHtml = k.scopes.map(s => {
        const cls = s.includes('write') ? 'scope-tag--write' : s === 'admin' ? 'scope-tag--admin' : 'scope-tag--read';
        return `<span class="scope-tag ${cls}">${s}</span>`;
      }).join('');
      const dotCls = k.status === 'active' ? 'dot--active' : k.status === 'revoked' ? 'dot--revoked' : 'dot--expired';
      const statusLabel = k.status === 'active' ? 'Активен' : k.status === 'revoked' ? 'Отозван' : 'Истёк';
      const masked = k.prefix.substring(0, 14) + '••••••••••••';
      return `
        <div class="key-row ${k.status !== 'active' ? 'revoked' : ''}">
          <div class="key-row__icon">🔑</div>
          <div class="key-row__body">
            <div class="key-row__name">${k.name}</div>
            <div class="key-row__key">${masked}</div>
            <div class="key-row__scopes">${scopeHtml}</div>
            <div class="key-row__meta">
              <span>Создан: ${k.created_at}</span>
              <span>Последнее использование: ${k.last_used_at || '—'}</span>
              <span>Истекает: ${k.expires_at || 'Никогда'}</span>
            </div>
          </div>
          <div class="key-row__right">
            <div class="status-dot">
              <span class="dot ${dotCls}"></span>
              <span style="color:${k.status==='active'?'var(--green)':k.status==='revoked'?'var(--muted)':'var(--red)'}">${statusLabel}</span>
            </div>
            <button class="btn-revoke" ${k.status !== 'active' ? 'disabled' : ''} onclick="revokeKey('${k.id}')">
              ${k.status === 'active' ? 'Отозвать' : 'Отозван'}
            </button>
          </div>
        </div>`;
    }).join('');
  }

  /* ── Create key ── */
  function createKey() {
    const name = document.getElementById('key_name').value.trim();
    let ok = true;
    if (!name) { document.getElementById('err_key_name').classList.add('visible'); ok = false; }
    else { document.getElementById('err_key_name').classList.remove('visible'); }

    const scopes = [...document.querySelectorAll('input[name="scope"]:checked')].map(i => i.value);
    if (!scopes.length) { document.getElementById('err_scopes').classList.add('visible'); ok = false; }
    else { document.getElementById('err_scopes').classList.remove('visible'); }

    if (!ok) return;

    /* Generate key: vkurse_ + 16 hex chars */
    const raw = 'vkurse_' + [...crypto.getRandomValues(new Uint8Array(8))].map(b => b.toString(16).padStart(2,'0')).join('');

    /* Expiry */
    let expiresAt = null;
    if (selectedExpiry !== 'never') {
      const d = new Date();
      if (selectedExpiry === '30d') d.setDate(d.getDate() + 30);
      else if (selectedExpiry === '90d') d.setDate(d.getDate() + 90);
      else if (selectedExpiry === '1y') d.setFullYear(d.getFullYear() + 1);
      expiresAt = d.toISOString().split('T')[0];
    }

    /* Add to list */
    keys.unshift({
      id: Date.now().toString(), name, prefix: raw.substring(0, 16),
      scopes, status: 'active',
      created_at: new Date().toISOString().split('T')[0],
      last_used_at: null, expires_at: expiresAt
    });

    /* Show key in modal (only time it's visible in plaintext) */
    document.getElementById('modalKeyValue').innerHTML =
      raw + `<button class="copy-btn" onclick="copyKey('${raw}')">Копировать</button>`;
    document.getElementById('modal').classList.add('visible');

    /* TODO: POST /api/v1/api-keys
       { name, scopes, expires_at }
       → api_keys table: id(UUID), user_id(FK→users.id), name, key_hash(bcrypt(raw)),
         key_prefix(first 16 chars), permissions(JSONB), is_active(true),
         created_at, expires_at */

    renderKeys();
    resetForm();
  }

  /* ── Revoke ── */
  function revokeKey(id) {
    if (!confirm('Отозвать ключ? Все запросы с этим ключом перестанут работать.')) return;
    const k = keys.find(k => k.id === id);
    if (k) { k.status = 'revoked'; }
    /* TODO: DELETE /api/v1/api-keys/:id  → api_keys.is_active = false */
    renderKeys();
    showToast('Ключ отозван');
  }

  /* ── Modal ── */
  function closeModal() {
    document.getElementById('modal').classList.remove('visible');
    showToast('Ключ создан и сохранён');
  }

  function copyKey(val) {
    const text = val || document.getElementById('modalKeyValue').firstChild.textContent.trim();
    navigator.clipboard.writeText(text).then(() => showToast('Ключ скопирован'));
  }

  /* ── Reset form ── */
  function resetForm() {
    document.getElementById('key_name').value = '';
    document.querySelectorAll('input[name="scope"]').forEach(i => { i.checked = i.value === 'news:read'; });
    document.querySelectorAll('.expiry-btn').forEach(b => b.classList.toggle('active', b.textContent === '90 дней'));
    selectedExpiry = '90d';
  }

  /* ── Toast ── */
  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('visible');
    setTimeout(() => t.classList.remove('visible'), 3000);
  }

  /* ── Init ── */
  setRole('admin');
  renderKeys();
</script>
</body>
</html>
