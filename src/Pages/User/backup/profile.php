<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL . '/static/css/profile-mert.css'?>" />
    <title>Профиль — spbVkurse</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar__logo">spb<span>Vkurse</span></div>
        <div class="navbar__right">
            <a href="/vkurse/user/me/settings" class="navbar__back"><i></i>Настройки</a>
            <div class="avatar-sm" id="navAvatar">
                <?= substr($_SESSION['user']['full_name'], 0, 1)?>
            </div>
            <div class="avatar-options-modal">
                <ul>
                    <li class="logout-btn btn-outline">Выйти</li>
                </ul>
            </div>
            <script>
                let avatar = document.querySelector('#navAvatar');
                let logoutBtn = document.querySelector('.logout-btn');
                avatar.addEventListener('click', ()=>{
                    document.querySelector('.avatar-options-modal').classList.toggle('show');
                });
                logoutBtn.onclick = () => {
                    location.href = '/vkurse/auth/logout';
                }
            </script>
        </div>
    </nav>

    <!-- Demo switcher -->
    
    <div class="demo-bar">
        <?php if(false):?>
        <strong>Demo:</strong>
        <span>Роль:</span>
        <button class="demo-btn active" onclick="setRole('reader')">
            Читатель
        </button>
        <button class="demo-btn" onclick="setRole('author')">Автор</button>
        <?php endif;?>
        <span style="margin-left: 8px">Подписка:</span>
        <button class="demo-btn active" id="btn-nosub" onclick="setSub(false)">
            Нет
        </button>
        <button class="demo-btn" id="btn-sub" onclick="setSub(true)">
            Активна
        </button>
        <?php if($_SESSION['user']['role'] === 'author'):?>
            <button class="demo-btn active" onclick="location.href= '/vkurse/user/author/admin/create'">
                Добавить Статью
            </button>
        <?php endif;?>
    </div>
    

    <main class="main">
        <!-- Hero -->
        <div class="hero">
            <div class="hero__avatar" id="heroAvatar" <?= $_SESSION['user']['avatar_url'] && 'style=background: url('. $_SESSION['user']['avatar_url'] .')'?>><?= substr(($_SESSION['user']['full_name']), 0, 1)?></div>
            <div class="hero__info">
            <div class="hero__name" id="heroName"><?= $_SESSION['user']['full_name']?></div>
            <div class="hero__email" id="heroEmail"><?= $_SESSION['user']['email']?></div>
            <div class="hero__badges">
                <span class="badge badge--reader" id="badgeRole"><?= $_SESSION['user']['role']?></span>
                <span class="badge badge--sub" id="badgeSub" style="display: none"
                >Premium</span
                >
            </div>
            </div>
            <div class="hero__actions">
            <button class="btn-outline" onclick="location.href = '/vkurse/user/me/settings'">
                <i class="fa-solid fa-gear"></i><span>Редактировать</span>
            </button>
            <button class="btn-outline" onclick="location.href = '/vkurse/user/me/inbox'">
                <i class="fa-solid fa-envelope"></i><span>Сообщения</span>
            </button>
            </div>
        </div>

        <!-- Top cards -->
        <div class="section">
            <div class="section-title">Быстрый доступ</div>
            <div class="top-cards" id="topCards">
            <!-- Подписки -->
            <a
                class="top-card top-card--accent"
                onclick="handleSubCard(event)"
                href="#"
            >
                <div class="top-card__icon">💎</div>
                <div class="top-card__label">Подписка</div>
                <div class="top-card__value" id="subCardValue">Нет</div>
                <div class="top-card__sub" id="subCardSub">
                Нажмите, чтобы подписаться
                </div>
            </a>

            <!-- Почта -->
            <a class="top-card" href="/vkurse/user/me/inbox">
                <div class="top-card__icon"><i class="fa-solid fa-envelope"></i></div>
                <div class="top-card__label">Входящие</div>
                <div class="top-card__value"><?= isset($_SESSION['user']['emails']) ? count($_SESSION['user']['emails']) : 0 ?></div>
                <div class="top-card__sub"><?= isset($_SESSION['user']['emails']) ? count($_SESSION['user']['emails']['unread']) : 0 ?> непрочитанных</div>
            </a>

            <!-- Статьи — только для автора -->
            <?php if($_SESSION['user']['role'] === 'author'):?>
            <a class="top-card" id="articlesCard" href="/user/author/admin/list" style="display: block">
                <div class="top-card__icon"><i class="fas fa-pencil"></i></div>
                <div class="top-card__label">Мои статьи</div>
                <div class="top-card__value"></div>
                <div class="top-card__sub">0 на модерации</div>
            </a>
            <?php endif;?>
            </div>
        </div>

        <!-- Subscription offer panel -->
        <?php if(!isset($_SESSION['user']['subscription'])):?>
            <?php if(isset($subscriptionPlans)):?>
        <div class="section sub-offer-panel" id="subOfferPanel">
            <div class="section-title">Оформить подписку</div>
            <?php foreach($subscriptionPlans as $plan):?>
                <div class="top-card" style="cursor: default">
                    <div
                    style="font-size: 1.1rem; font-weight: 700; margin-bottom: 6px"
                    >
                    <?= $plan['name']?>
                    </div>
                    <div
                    style="font-size: 2rem; font-weight: 800; color: var(--accent2)"
                    >
                    <?= $plan['price']?> ₽
                    </div>
                    <div
                    style="
                        font-size: 0.78rem;
                        color: var(--muted);
                        margin: 8px 0 16px;
                    "
                    >
                    отмена в любое время
                    </div>
                    <button
                    class="btn-follow"
                    style="width: 100%"
                    onclick="activateSub()"
                    >
                    Оформить
                    </button>
                </div>
            <?php endforeach;?>
            </div>
        </div>
            <?php endif;?>
        <?php else:?>
        <!-- Subscription info panel -->
            <div class="section sub-info-panel" id="subInfoPanel">
                <div class="section-title">Информация о подписке</div>
                <div class="info-box">
                <div class="info-box__title"><?= $_SESSION['user']['subscription']['status'] === 'active' ? 'Подписка активна' : 'Подписка не активна' ?></div>
                <div class="info-row">
                    <span class="info-row__label">Тарифный план</span
                    ><span class="info-row__value"><?= $_SESSION['user']['subscription']['plan'] ?></span>
                </div>
                <div class="info-row">
                    <span class="info-row__label">Статус</span
                    ><span class="info-row__value" style="color: var(--green)"
                    ><?= $_SESSION['user']['subscription']['status']?></span
                    >
                </div>
                <div class="info-row">
                    <span class="info-row__label">Дата начала</span
                    ><span class="info-row__value"><?= $_SESSION['user']['subscription']['started_at']?></span>
                </div>
                <div class="info-row">
                    <span class="info-row__label">Дата окончания</span
                    ><span class="info-row__value"><?= $_SESSION['user']['subscription']['expires_at']?></span>
                </div>
                <div class="info-row">
                    <span class="info-row__label">Автопродление</span
                    ><span class="info-row__value" style="color: var(--green)"
                    ><?= $_SESSION['user']['subscription']['auto_renew'] ? 'Включено' : 'не Включено' ?></span
                    >
                </div>
                <div class="info-row">
                    <span class="info-row__label">Способ оплаты</span
                    ><span class="info-row__value"><?= $_SESSION['user']['subscription']['payment_method']?></span>
                </div>
                </div>
            </div>
        <?php endif;?>
        

        <!-- Recent searches + Recommendations -->
        <div class="section">
            <div class="section-title">Недавние поиски</div>
            <div class="searches">
            <?php if(empty($_SESSION['user']['read_history'])):?>
            <div class="message"><h1>Empty Search history. Begin finding you favorite articles</h1></div>
            <?php else:
            foreach($_SESSION['user']['read_history'] as $search):?>
                <div class="search-tag"><span class="icon">🔍</span><?= $search['title']?></div>
            <?php endforeach; endif;?>
            </div>
        </div>

        <?php if($_SESSION['user']['role'] !== 'author'):?>
        <div class="section">
            <div class="section-title">Рекомендации на основе поисков</div>
            <div class="rec-grid">
            <div class="rec-card">
                <div class="rec-card__img">
                ⛓
                <div class="rec-card__tag">Технологии</div>
                </div>
                <div class="rec-card__body">
                <div class="rec-card__title">
                    Как блокчейн меняет финансовый сектор в 2026 году
                </div>
                <div class="rec-card__meta">Дмитрий Козлов · 5 мин</div>
                </div>
            </div>
            <div class="rec-card">
                <div class="rec-card__img">
                🤖
                <div class="rec-card__tag">ИИ</div>
                </div>
                <div class="rec-card__body">
                <div class="rec-card__title">
                    GPT-5 и будущее автоматизации: чего ожидать
                </div>
                <div class="rec-card__meta">Анна Смирнова · 8 мин</div>
                </div>
            </div>
            <div class="rec-card">
                <div class="rec-card__img">
                💹
                <div class="rec-card__tag">Экономика</div>
                </div>
                <div class="rec-card__body">
                <div class="rec-card__title">
                    Российские стартапы привлекли рекордные инвестиции
                </div>
                <div class="rec-card__meta">Иван Петров · 4 мин</div>
                </div>
            </div>
            </div>
        </div>
        <?php endif;?>

        <!-- Articles from followed authors -->
        <?php if(isset($_SESSION['user']['followed_authors'])):?>
        <div class="section">
            <div class="section-title">Статьи авторов, на которых вы подписаны</div>
            <?php if(empty($_SESSION['user']['followed_authors'])):?>
            <div><h1>Вы не подписаны на никаких авторов</h1></div>
            <?php else:?>
            <div class="articles-list">
            <?php foreach($_SESSION['user']['followed_authors'] as $author):?>
                <?php foreach($author['articles'] as $article):?>
                <div class="article-row">
                    <div
                    class="article-row__avatar"
                    style="background: linear-gradient(135deg, #7c6af7, #ec4899)"
                    >
                    <?= substr($author['full_name'], 0, 2)?>
                    </div>
                    <div class="article-row__body">
                    <div class="article-row__author"><?= $aticle['author']?>· <?= $article['published_at']?></div>
                    <div class="article-row__title">
                        <?= $article['title']?>
                    </div>
                    <div class="article-row__meta">
                        <span><i class="fa-solid fa-eye"></i> <?= $article['views_count']?></span>
                        <span><i class="fa-solid fa-heart"></i> <?= $article['likes_count']?></span>
                        <span><i class="fa-solid fa-"></i> <?= $article['comments_count']?></span>
                    </div>
                    </div>
                    <span class="article-row__status status--published">published</span>
                </div>
                <?php endforeach;?>
            <?php endforeach; endif;?>
            </div>
        </div>
        <?php endif;?>

        <!-- Popular authors -->
        <?php if($_SESSION['user']['role'] !== 'author'):?>
        <div class="section">
            <div class="section-title">Популярные авторы</div>
            <div class="authors-grid">
            <?php foreach($authors as $author):?>
                <div class="author-card">
                    <div
                    class="author-card__avatar"
                    style="background: linear-gradient(135deg, #7c6af7, #ec4899)"
                    >
                    <?= substr($author['name'], 0,2)?>
                    </div>
                    <div class="author-card__name"><?= $author['full_name']?></div>
                    <div class="author-card__articles"><?= $author['article_count']?> статей · <?= $author['subcriptions_count']?> подписчиков</div>
                    <button class="btn-follow following" onclick="toggleFollow(this)">
                    ✓ Подписан
                    </button>
                </div>
            <?php endforeach;?>
            </div>
        </div>
        <?php endif;?>
    </main>

    <script>
        let currentRole = "<?= $_SESSION['user']['role']?>";
        let hasSub = <?= isset($_SESSION['user']['subscription'])?>;

        function setRole(role) {
            currentRole = role;
            document.querySelectorAll(".demo-bar .demo-btn").forEach((b) => {
            if (b.textContent === "Читатель" || b.textContent === "Автор")
                b.classList.toggle(
                "active",
                b.textContent
                    .toLowerCase()
                    .includes(role === "reader" ? "чита" : "авт"),
                );
            });
            document.getElementById("articlesCard").style.display =
            role === "author" ? "block" : "none";
            document.getElementById("badgeRole").textContent =
            role === "author" ? "Автор" : "Читатель";
            document.getElementById("badgeRole").className =
            "badge " + (role === "author" ? "badge--author" : "badge--reader");
        }

        function setSub(active) {
            hasSub = active;
            document.getElementById("btn-sub").classList.toggle("active", active);
            document
            .getElementById("btn-nosub")
            .classList.toggle("active", !active);
            document.getElementById("badgeSub").style.display = active
            ? "inline-block"
            : "none";
            document.getElementById("subCardValue").textContent = active
            ? "Активна"
            : "Нет";
            document.getElementById("subCardSub").textContent = active
            ? "Ежегодный план · до 01.01.2027"
            : "Нажмите, чтобы подписаться";
            document
            .getElementById("subInfoPanel")
            .classList.toggle("visible", active);
            document.getElementById("subOfferPanel").classList.remove("visible");
        }

        function handleSubCard(e) {
            e.preventDefault();
            if (hasSub) {
            document.getElementById("subInfoPanel").classList.add("visible");
            document.getElementById("subOfferPanel").classList.remove("visible");
            } else {
            document.getElementById("subOfferPanel").classList.add("visible");
            document.getElementById("subInfoPanel").classList.remove("visible");
            }
            document
            .getElementById("subOfferPanel")
            .scrollIntoView({ behavior: "smooth", block: "start" });
        }

        function activateSub() {
            setSub(true);
            document.getElementById("subOfferPanel").classList.remove("visible");
            document.getElementById("subInfoPanel").classList.add("visible");
        }

        function toggleFollow(btn) {
            const following = btn.classList.contains("following");
            btn.classList.toggle("following", !following);
            btn.textContent = following ? "+ Подписаться" : "✓ Подписан";
        }
    </script>
</body>
</html>
