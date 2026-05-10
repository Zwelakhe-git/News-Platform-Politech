/* ---- Password toggle ---- */
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') { inp.type = 'text'; btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>'; }
    else { inp.type = 'password'; btn.innerHTML = '<i class="fa-solid fa-eye"></i>'; }
}

let loginLink = document.querySelector('.login-link');
let formTypeToggler = loginLink.querySelector('.form-type-toggler');
let submitButton = document.querySelector("#submitBtn");
let formType = 'register';
let formHeading = document.querySelector('.form-heading');
let roleField = document.querySelector('.role-field')
/* ------ change form UI ------ */
function changeFormUI(formType) {
    let passwdConfirmField = document.querySelector('#password_confirm');
    while(!passwdConfirmField.classList.contains('field')){
        passwdConfirmField = passwdConfirmField.parentElement;
    }

    if (formType === 'login') {
        passwdConfirmField.style.removeProperty('display');
        loginLink.querySelector('.login-link-text').textContent = 'Уже есть аккаунт?';
        formTypeToggler.textContent = 'Войти';
        formType = 'register';
        submitButton.textContent = 'Зарегистрироваться';
        formHeading.innerHTML = '<h1>Создать аккаунт</h1><p>Заполните форму — это займёт меньше минуты</p>';
        roleField.style.removeProperty('display');
    } else {
        passwdConfirmField.style.setProperty('display', 'none');
        loginLink.querySelector('.login-link-text').textContent = 'Нет Аккаунта?';
        formTypeToggler.textContent = 'Зарегистрироваться';
        formType = 'login';
        submitButton.textContent = 'Логин';
        formHeading.innerHTML = '<h1>Войти в свой аккаунт</h1>';
        roleField.style.setProperty('display', 'none');
        resetStrengthFill();
    }
    return formType;
}

function resetStrengthFill() {
    document.getElementById('strengthFill').style.width = '0';
    document.getElementById('strengthText').textContent = '';
}

formTypeToggler.addEventListener('click', function () {
    formType = changeFormUI(formType);
});

/* ---- initialise form ---- */
formType = changeFormUI(formType);
function removeAlerts() {
    return new Promise(res => {
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(al => {
                al.classList.remove('visible');
                setTimeout(()=>{al.classList.remove('success', 'fail')}, 300);
            });
            res(true);
        }, 2000);
    });
}
/* ── Password toggle ── */
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') { inp.type = 'text';  btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>'; }
    else                         { inp.type = 'password'; btn.innerHTML = '<i class="fa-solid fa-eye"></i>'; }
}

/* ── Strength ── */
document.getElementById('password').addEventListener('input', function () {
    if(formType === 'login'){
      return;
    }
    const v = this.value;
    const fill = document.getElementById('strengthFill');
    const label = document.getElementById('strengthText');
    let score = 0;
    if (v.length >= 8) score++;
    if (v.length >= 12) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    const levels = [
        { w: '0%', bg: 'transparent', text: '', col: 'var(--muted)' },
        { w: '20%', bg: '#f87171', text: 'Очень слабый', col: '#f87171' },
        { w: '40%', bg: '#fb923c', text: 'Слабый', col: '#fb923c' },
        { w: '65%', bg: '#fbbf24', text: 'Средний', col: '#fbbf24' },
        { w: '85%', bg: '#34d399', text: 'Хороший', col: '#34d399' },
        { w: '100%', bg: '#10b981', text: 'Отличный', col: '#10b981' },
    ];
    const l = levels[Math.min(score, 5)];
    fill.style.width = l.w;
    fill.style.background = l.bg;
    label.textContent = l.text;
    label.style.color = l.col;
});

/* ── Avatar preview ── */
document.getElementById('avatar_url').addEventListener('input', function () {
    const ring = document.getElementById('avatarRing');
    const img = document.getElementById('avatarImg');
    const url = this.value.trim();
    if (url) {
        img.src = url;
        ring.style.display = 'block';
        img.onerror = () => { ring.style.display = 'none'; };
    } else {
        ring.style.display = 'none';
    }
});

/* ── Custom checkbox ── */
function toggleCheck() {
    const cb = document.getElementById('agree_terms');
    const box = document.getElementById('checkBox');
    cb.checked = !cb.checked;
    box.classList.toggle('checked', cb.checked);
    if (cb.checked) document.getElementById('err_terms').classList.remove('show');
}

/* ── Helpers ── */
function setErr(id, show) {
    document.getElementById('err_' + id).classList.toggle('show', show);
    document.getElementById(id).classList.toggle('err', show);
    return !show;
}
const validEmail = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
const validUrl = v => { try { new URL(v); return true; } catch { return false; } };

/* ── Submit ── */
document.getElementById('registerForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    let ok = true;

    const fullName = document.getElementById('full_name').value.trim();
    ok &= setErr('full_name', fullName.length < 2);

    const email = document.getElementById('email').value.trim();
    ok &= setErr('email', !validEmail(email));

    const pw = document.getElementById('password').value;
    ok &= ((formType === 'login') || setErr('password', pw.length < 8));

    const pw2 = document.getElementById('password_confirm').value;
    ok &= ((formType === 'login') || setErr('password_confirm', pw !== pw2 || pw2 === ''));

    const avatarUrl = document.getElementById('avatar_url').value.trim();
    ok &= ((formType === 'login') || setErr('avatar_url', avatarUrl !== '' && !validUrl(avatarUrl)));

    const terms = document.getElementById('agree_terms').checked;
    document.getElementById('err_terms').classList.toggle('show', !terms);
    ok &= terms;

    if (!ok) return;

    const payload = {
        full_name: fullName,
        email: email,
        password: pw,        // backend: → password_hash
        role: document.querySelector('input[name="role"]:checked').value,
        avatar_url: avatarUrl || null,
    };

    let formData = new FormData();
    for (const [k, v] of Object.entries(payload)) {
        formData.append(k, v);
    }

    //console.log('Registration payload:', payload);
    let url = formType === 'login' ? '/vkurse/auth/login' : '/vkurse/auth/register'
    let btnText = submitButton.textContent;
    submitButton.innerHtml = '<i class="fa-solid fa-spinner fa-spin"></i>';

    let response = await fetch(url, {
        method: 'POST',
        body: formData
    });
    try {
        let data = await response.json();
        // console.log(data);
        let rb = document.getElementById('responseBanner');
        if (data.success) {
            rb.innerHTML = `<span>${data.message}</span>`;
            rb.classList.add('visible', 'success');

            await removeAlerts();
            if (formType === 'login') {
                location.href = '/vkurse/user/me';
            }
        } else {
            rb.innerHTML = `<span>${data.message ?? 'Registration failed'}</span>`;
            rb.classList.add('visible', 'fail');
        }
        removeAlerts();

        //submitButton.disabled = true;
        submitButton.textContent = btnText;
        //this.reset();
        document.getElementById('avatarPreview').style.display = 'none';
        //resetStrengthFill();

    } catch (err) {
        console.log('authentication error: ' + err);
    }

    // document.getElementById('successBanner').classList.add('show');
    // document.getElementById('submitBtn').disabled = true;
    // this.reset();
    // document.getElementById('avatarRing').style.display = 'none';
    // document.getElementById('strengthFill').style.width = '0';
    // document.getElementById('strengthText').textContent = '';
    // document.getElementById('checkBox').classList.remove('checked');
    // window.scrollTo({ top: 0, behavior: 'smooth' });
});