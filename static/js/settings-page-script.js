/* ── Tabs ── */
function showTab(name, el) {
    document.querySelectorAll('.settings-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.sidebar-nav__item').forEach(i => i.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    el.classList.add('active');
}

/* ── Avatar preview ── */
function previewAvatar(url) {
    const img = document.getElementById('avatarImg');
    const big = document.getElementById('avatarBig');
    if (url) {
    img.src = url;
    img.style.display = 'block';
    img.onerror = () => { img.style.display = 'none'; };
    } else {
    img.style.display = 'none';
    }
}

/* ── Password toggle ── */
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.innerHTML = inp.type === 'password' ? '<i class="fa-solid fa-eye"></i>' : '<i class="fa-solid fa-eye-slash"></i>';
}

/* ── Password strength ── */
function checkStrength(v) {
    let score = 0;
    if (v.length >= 8) score++;
    if (v.length >= 12) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    const levels = [
    { w: '0%', bg: 'transparent', text: '' },
    { w: '25%', bg: '#f87171', text: 'Очень слабый' },
    { w: '50%', bg: '#fbbf24', text: 'Слабый' },
    { w: '75%', bg: '#fbbf24', text: 'Средний' },
    { w: '90%', bg: '#34d399', text: 'Хороший' },
    { w: '100%', bg: '#34d399', text: 'Отличный' },
    ];
    const l = levels[Math.min(score, 5)];
    document.getElementById('pwStrengthBar').style.cssText = `width:${l.w};background:${l.bg}`;
    const lbl = document.getElementById('pwStrengthLabel');
    lbl.textContent = l.text;
    lbl.style.color = l.bg;
}

/* ── Validation ── */
function showErr(id, show) {
    const el = document.getElementById('err_' + id);
    if (el) el.classList.toggle('visible', show);
    return !show;
}
function isEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }
function isUrl(v) { try { new URL(v); return true; } catch { return false; } }

/* ── Save profile ── */
async function saveProfile() {
    let ok = true;
    const name = document.getElementById('full_name').value.trim();
    ok &= showErr('full_name', name.length < 2);
    const email = document.getElementById('email').value.trim();
    ok &= showErr('email', !isEmail(email));
    //const av = document.getElementById('avatar_url').value.trim();
    //if (av) ok &= showErr('avatar', !isUrl(av));

    if (!ok) return;
    /* TODO: PATCH /api/users/me  { full_name, email, avatar_url } */
    toast('Профиль обновлён');
}

function resetProfile() {
    document.getElementById('full_name').value = 'Мерт Гюнеш';
    document.getElementById('email').value = 'mert@example.com';
    document.getElementById('avatar_url').value = '';
    document.getElementById('avatarImg').style.display = 'none';
}

/* ── Save password ── */
function savePassword() {
    let ok = true;
    const pw = document.getElementById('pw_new').value;
    ok &= showErr('pw_new', pw.length < 8);
    const pw2 = document.getElementById('pw_confirm').value;
    ok &= showErr('pw_confirm', pw !== pw2 || !pw2);
    if (!ok) return;
    /* TODO: POST /api/auth/change-password  { current_password, new_password } → hash stored as password_hash */
    toast('Пароль обновлён');
    ['pw_current','pw_new','pw_confirm'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('pwStrengthBar').style.width = '0';
    document.getElementById('pwStrengthLabel').textContent = '';
}

/* ── Save notifications ── */
function saveNotifications() {
    const payload = {
    push_enabled:                 document.getElementById('push_enabled').checked,
    email_enabled:                document.getElementById('email_enabled').checked,
    daily_digest:                 document.getElementById('daily_digest').checked,
    notify_on_followed_authors:   document.getElementById('notify_followed').checked,
    notify_on_breaking:           document.getElementById('notify_breaking').checked,
    };
    /* TODO: PUT /api/users/me/notification-settings  payload → user_notification_settings table */
    console.log('Notification settings:', payload);
    toast('Настройки уведомлений сохранены');
}

/* ── Delete account ── */
function confirmDelete() {
    if (confirm('Вы уверены? Это действие необратимо.')) {
    /* TODO: DELETE /api/users/me */
    toast('Запрос на удаление отправлен');
    }
}

/* ── Toast ── */
function toast(msg) {
    const el = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    el.classList.add('visible');
    setTimeout(() => el.classList.remove('visible'), 3000);
}