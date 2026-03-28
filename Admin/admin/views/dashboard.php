<?php
//require_once ADMIN_PATH . '/views/layout/header.php'; ?>

<div class="row">
    <div class="stats card-grid">
        <div class="stats-card">
            <div class="card-header"><h5>Emails</h5></div>
            <div class="card-body">my emails</div>
            <div class="card-footer"><?= explode(" ", date('Y-m-d H:i:s'), 2)[0]?></div>
        </div>
        <div class="stats-card">
            <div class="card-header"><h5>Subscriptions</h5></div>
            <div class="card-body">my subscriptions</div>
            <div class="card-footer"><?= explode(" ", date('Y-m-d H:i:s'), 2)[0]?></div>
        </div>
        <div class="stats-card">
            <div class="card-header"><h5>Notifications</h5></div>
            <div class="card-body">notifications</div>
            <div class="card-footer"><?= explode(" ", date('Y-m-d H:i:s'), 2)[0]?></div>
        </div>
    </div>
</div>

<div class="search-history section">
    <div class="section-header">
        <h2>Recent Searches</h2>
    </div>
    <div class="grid">
        <a class="article-card">
            <div class="article-card-image">
                <img alt="article-image" src="https://images.news.ru/2026/03/26/evn1P2qKMUWkN1rlhwIYdRsCt9iUgI1q94b45flX_450.jpg"/>
            </div>
            <div class="article-card__body">
                <div class="article-info">
                    <div class="article-category">Politics</div>
                    <div class="article-date">
                        <i class="fa-regular fa-clock"></i>
                        <span><?= date('Y-m-d H:i:s')?></span>
                    </div>
                </div>
                <div class="article-card-descr">
                    <p>В австралийском городе Карнарвон небо окрасилось в насыщенный красный цвет из-за мощного погодного явления, вызванного тропическим циклоном, сообщает ABC News. Сильные ветры подняли в воздух огромное количество пыли, в результате чего на протяжении нескольких часов над регионом сохранялась густая пылевая завеса, придавшая небу необычный оттенок.</p>
                </div>
            </div>
            <div class="article-card__footer">
                <div class="article-card-date"></div>
                <div class="article-card-author">
                    <div class="article-card-author-avatar">
                        <img alt="author avatar" src="https://images.news.ru/2026/03/28/mRI5smcoidEDRPMttbxGdaahgXKZFLZyoR2kbc0p_450.jpeg"/>
                    </div>
                    <div class="article-card-author-name">John Doe</div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="author-content section">
    <div class="section-header">
        <h2>From your Favorite Authors</h2>
    </div>
    <div class="grid">
        <a class="article-card">
            <div class="article-card-image">
                <img alt="article-image" src="https://images.news.ru/2026/03/26/evn1P2qKMUWkN1rlhwIYdRsCt9iUgI1q94b45flX_450.jpg"/>
            </div>
            <div class="article-card__body">
                <div class="article-info">
                    <div class="article-category">Politics</div>
                    <div class="article-date">
                        <i class="fa-regular fa-clock"></i>
                        <span><?= date('Y-m-d H:i:s')?></span>
                    </div>
                </div>
                <div class="article-card-descr">
                    <p>В австралийском городе Карнарвон небо окрасилось в насыщенный красный цвет из-за мощного погодного явления, вызванного тропическим циклоном, сообщает ABC News. Сильные ветры подняли в воздух огромное количество пыли, в результате чего на протяжении нескольких часов над регионом сохранялась густая пылевая завеса, придавшая небу необычный оттенок.</p>
                </div>
            </div>
            <div class="article-card__footer">
                <div class="article-card-date"></div>
                <div class="article-card-author">
                    <div class="article-card-author-avatar">
                        <img alt="author avatar" src="https://images.news.ru/2026/03/28/mRI5smcoidEDRPMttbxGdaahgXKZFLZyoR2kbc0p_450.jpeg"/>
                    </div>
                    <div class="article-card-author-name">John Doe</div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="recomendations section">
    <div class="section-header">
        <h2>Popular Authors</h2>
    </div>
    <div class="grid">
        <div class="author-card"></div>
    </div>
</div>

<div class="Promotions section">
    <div class="section-header">
        <h2>Don't miss out</h2>
    </div>
    <div class="video grid">
        <div class="video-card"></div>
    </div>
</div>
<!-- <?php if(isset($user['role']) || $user['role'] !== 'author'){?>
<div class="row quick-actions">
    <div></div>
</div>
<?php }?> -->
