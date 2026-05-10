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
    <a class="top-card" id="articlesCard" href="#" style="display: block">
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
<?php if( $_SESSION['user']['role'] !== 'author'):?>
<div class="section">
    <div class="section-title">Недавние поиски</div>
    <div class="searches">
        <?php if(empty($_SESSION['user']['read_history'])):?>
        <div class="message"><h1>История поисков пуста</h1></div>
        <?php else:
        foreach($_SESSION['user']['read_history'] as $search):?>
            <div class="search-tag"><span class="icon">🔍</span><?= $search['title']?></div>
        <?php endforeach; endif;?>
    </div>
</div>
<?php endif;?>

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