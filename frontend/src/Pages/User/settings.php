

    <nav class="navbar">
        <div class="navbar__logo">spb<span>Vkurse</span></div>
        <a href="/vkurse/user/me" class="navbar__back">← Профиль</a>
    </nav>

    <div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <nav class="sidebar-nav">
        <div class="sidebar-nav__item active" onclick="showTab('profile', this)">
            <span class="sidebar-nav__icon"><i class="fa-solid fa-profile"></i></span> Профиль
        </div>
        <div class="sidebar-nav__item" onclick="showTab('security', this)">
            <span class="sidebar-nav__icon"><i class="fa-solid fa-shield"></i></span> Безопасность
        </div>
        <div class="sidebar-nav__item" onclick="showTab('notifications', this)">
            <span class="sidebar-nav__icon"><i class="fa-solid fa-bell"></i></span> Уведомления
        </div>
        <div class="sidebar-nav__item" onclick="showTab('danger', this)">
            <span class="sidebar-nav__icon"><i class="fa-solid fa-triangle-exclamation"></i></span> Аккаунт
        </div>
        </nav>
    </aside>

    <!-- Content -->
    <div class="content">

        <!-- ── PROFILE ── -->
        <div class="settings-section active" id="tab-profile">
        <div class="section-header">
            <h2>Настройки профиля</h2>
            <p>Управляйте своей личной информацией</p>
        </div>

        <div class="card">
            <div class="card__title">Аватар</div>
            <div class="avatar-row">
            <div class="avatar-big" id="avatarBig">
                <?= substr($_SESSION['user']['email'],0,1)?>
                <img id="avatarImg" src="" alt="" />
                <div class="image-upload-area" style="
                                display: absolute;
                                border-radius: 50%;
                                border-top-radius: 0px;
                                opacity: 0.6;
                                color: grey;
                                bottom: 0px;
                                top: 25%;
                "><i class="fa-solid fa-camera"></i></div>
                <script>
                    document.querySelector('.image-upload-area').addEventListener('click', function(){
                        document.querySelector('input[name=avatar_image]').click();
                    });
                </script>
            </div>
            <div>
                <div style="font-size:.85rem;font-weight:600;margin-bottom:4px">Фото профиля</div>
                <div style="font-size:.75rem;color:var(--muted)">Вставьте URL изображения ниже</div>
            </div>
            </div>
            <!-- avatar_url — users table -->
            <div class="form-group">
            <label for="avatar-image">Avatar URL</label>
            <!-- <input type="url" id="avatar_url" name="avatar_url"
                    placeholder="https://example.com/photo.jpg"
                    oninput="previewAvatar(this.value)" /> -->
            <input type="file" id="avatar-image" name="avatar_image" style="display: none"/>
            <div class="input-hint">загрузите изображение профиля</div>
            <div class="error-msg" id="err_avatar">изображение не загрузилосьL</div>
            </div>
        </div>

        <div class="card">
            <div class="card__title">Личные данные</div>
            <!-- full_name — users.full_name VARCHAR(100) -->
            <div class="form-group">
            <label for="full_name">Полное имя <span class="req">*</span></label>
            <input type="text" id="full_name" name="full_name"
                    value="<?= $_SESSION['user']['full_name']?>" maxlength="100" placeholder="Иван Иванов" />
            <div class="input-hint">users.full_name · макс. 100 символов</div>
            <div class="error-msg" id="err_full_name">Минимум 2 символа</div>
            </div>
            <!-- email — users.email VARCHAR(255) UNIQUE -->
            <div class="form-group">
            <label for="email">Email <span class="req">*</span></label>
            <input type="email" id="email" name="email"
                    value="<?= $_SESSION['user']['email']?>" maxlength="255" placeholder="ivan@example.com" />
            <div class="input-hint">users.email · уникальный · макс. 255 символов</div>
            <div class="error-msg" id="err_email">Введите корректный email</div>
            </div>
            <!-- role — users.role ENUM — readonly, показываем текущую роль -->
            <div class="form-group">
            <label>Роль на платформе</label>
            <div class="role-display">
                <span class="badge badge--reader" id="roleDisplay"><?= $_SESSION['user']['role']?></span>
                <span style="font-size:.85rem;color:var(--muted)">reader</span>
                <span class="role-display__note">Изменяется администратором</span>
            </div>
            <div class="input-hint">users.role · ('reader','author','moderator','admin')</div>
            </div>
            <div class="form-actions">
            <button class="btn-primary" onclick="saveProfile()">Сохранить изменения</button>
            <button class="btn-ghost" onclick="resetProfile()">Отмена</button>
            </div>
        </div>
        </div>

        <!-- ── SECURITY ── -->
        <div class="settings-section" id="tab-security">
        <div class="section-header">
            <h2>Безопасность</h2>
            <p>Управление паролем и защитой аккаунта</p>
        </div>

        <div class="card">
            <div class="card__title">Изменить пароль</div>
            <!-- password_hash — users.password_hash VARCHAR(255) -->
            <div class="form-group">
            <label for="pw_current">Текущий пароль <span class="req">*</span></label>
            <div class="password-wrap">
                <input type="password" id="pw_current" placeholder="Введите текущий пароль" />
                <button type="button" class="toggle-pw" onclick="togglePw('pw_current',this)"><i class="fa-solid fa-eye"></i></button>
            </div>
            </div>
            <div class="form-row">
            <div class="form-group">
                <label for="pw_new">Новый пароль <span class="req">*</span></label>
                <div class="password-wrap">
                <input type="password" id="pw_new" placeholder="Минимум 8 символов"
                        oninput="checkStrength(this.value)" />
                <button type="button" class="toggle-pw" onclick="togglePw('pw_new',this)">👁</button>
                </div>
                <div style="height:4px;border-radius:4px;background:var(--surface2);margin-top:8px;overflow:hidden">
                <div id="pwStrengthBar" style="height:100%;border-radius:4px;width:0;transition:width .3s,background .3s"></div>
                </div>
                <div id="pwStrengthLabel" style="font-size:.72rem;color:var(--muted);margin-top:4px"></div>
                <div class="error-msg" id="err_pw_new">Минимум 8 символов</div>
            </div>
            <div class="form-group">
                <label for="pw_confirm">Подтверждение <span class="req">*</span></label>
                <div class="password-wrap">
                <input type="password" id="pw_confirm" placeholder="Повторите пароль" />
                <button type="button" class="toggle-pw" onclick="togglePw('pw_confirm',this)">👁</button>
                </div>
                <div class="error-msg" id="err_pw_confirm">Пароли не совпадают</div>
            </div>
            </div>
            <div class="input-hint" style="margin-bottom:16px">Хранится как users.password_hash (bcrypt)</div>
            <button class="btn-primary" onclick="savePassword()">Обновить пароль</button>
        </div>

        <div class="card">
            <div class="card__title">Информация о сессии</div>
            <div class="toggle-row" style="border:none;padding:0">
            <div class="toggle-row__info">
                <div class="toggle-row__label">Последний вход</div>
                <div class="toggle-row__desc">users.last_login · 03.04.2026, 14:41</div>
            </div>
            </div>
        </div>
        </div>

        <!-- ── NOTIFICATIONS ── -->
        <div class="settings-section" id="tab-notifications">
        <div class="section-header">
            <h2>Уведомления</h2>
            <p>Настройте, как и когда получать уведомления</p>
        </div>

        <!-- user_notification_settings table -->
        <div class="card">
            <div class="card__title">Каналы · user_notification_settings</div>

            <!-- push_enabled BOOLEAN -->
            <div class="toggle-row">
            <div class="toggle-row__info">
                <div class="toggle-row__label">Push-уведомления</div>
                <div class="toggle-row__desc">push_enabled · уведомления в браузере</div>
            </div>
            <label class="toggle">
                <input type="checkbox" id="push_enabled" checked />
                <span class="toggle__slider"></span>
            </label>
            </div>

            <!-- email_enabled BOOLEAN -->
            <div class="toggle-row">
            <div class="toggle-row__info">
                <div class="toggle-row__label">Email-уведомления</div>
                <div class="toggle-row__desc">email_enabled · письма на почту</div>
            </div>
            <label class="toggle">
                <input type="checkbox" id="email_enabled" checked />
                <span class="toggle__slider"></span>
            </label>
            </div>
        </div>

        <div class="card">
            <div class="card__title">Типы уведомлений · notifications.type</div>

            <!-- daily_digest BOOLEAN -->
            <div class="toggle-row">
            <div class="toggle-row__info">
                <div class="toggle-row__label">Ежедневный дайджест</div>
                <div class="toggle-row__desc">daily_digest · сводка новостей за день</div>
            </div>
            <label class="toggle">
                <input type="checkbox" id="daily_digest" checked />
                <span class="toggle__slider"></span>
            </label>
            </div>

            <!-- notify_on_followed_authors BOOLEAN -->
            <div class="toggle-row">
            <div class="toggle-row__info">
                <div class="toggle-row__label">Новые статьи авторов</div>
                <div class="toggle-row__desc">notify_on_followed_authors · type: new_article</div>
            </div>
            <label class="toggle">
                <input type="checkbox" id="notify_followed" checked />
                <span class="toggle__slider"></span>
            </label>
            </div>

            <!-- notify_on_breaking BOOLEAN -->
            <div class="toggle-row">
            <div class="toggle-row__info">
                <div class="toggle-row__label">Срочные новости</div>
                <div class="toggle-row__desc">notify_on_breaking · type: breaking_news</div>
            </div>
            <label class="toggle">
                <input type="checkbox" id="notify_breaking" checked />
                <span class="toggle__slider"></span>
            </label>
            </div>

            <!-- subscription_expiring -->
            <div class="toggle-row">
            <div class="toggle-row__info">
                <div class="toggle-row__label">Истечение подписки</div>
                <div class="toggle-row__desc">type: subscription_expiring · за 3 дня до окончания</div>
            </div>
            <label class="toggle">
                <input type="checkbox" id="notify_sub" checked />
                <span class="toggle__slider"></span>
            </label>
            </div>
        </div>

        <button class="btn-primary" onclick="saveNotifications()">Сохранить настройки</button>
        </div>

        <!-- ── DANGER ZONE ── -->
        <div class="settings-section" id="tab-danger">
        <div class="section-header">
            <h2>Управление аккаунтом</h2>
            <p>Необратимые действия с аккаунтом</p>
        </div>

        <!-- is_blocked BOOLEAN -->
        <div class="card" style="margin-bottom:12px">
            <div class="card__title">Статус аккаунта · users.is_blocked</div>
            <div class="toggle-row" style="border:none;padding:0">
            <div class="toggle-row__info">
                <div class="toggle-row__label">Аккаунт активен</div>
                <div class="toggle-row__desc">users.is_blocked = false · создан: 01.01.2026</div>
            </div>
            <span class="badge badge--reader" style="padding:4px 12px;border-radius:20px">Активен</span>
            </div>
        </div>

        <div class="danger-card">
            <div class="danger-card__title">Опасная зона</div>
            <div class="danger-row">
            <div class="danger-row__text">
                <h4>Удалить аккаунт</h4>
                <p>Все данные будут удалены безвозвратно. Это действие нельзя отменить.</p>
            </div>
            <button class="btn-danger" onclick="confirmDelete()">Удалить аккаунт</button>
            </div>
        </div>
        </div>

    </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast"><i class="fa-solid fa-circle-check"></i><span id="toastMsg">Изменения сохранены</span></div>

    <script src="<?= BASE_URL . '/static/js/settings-page-script.js'?>"></script>
