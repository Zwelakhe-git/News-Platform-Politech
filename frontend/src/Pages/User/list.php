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
    <div class="section">
        <div class="section-title">Мои статьи</div>
        <?php if(!isset($articles) || empty($articles)):?>
        <div><h1>нет статьи</h1></div>
        <?php else:?>
        <div class="rec-grid">
            <?php foreach($articles as $article):?>
            <div class="rec-card">
                <div class="rec-card__img">
                    <div class="rec-card__tag"><?= $article['category']?></div>
                </div>
                <div class="rec-card__body">
                    <div class="rec-card__title">
                        <?= $article['title']?>
                    </div>
                    <div class="rec-card__meta"><?= $article['author']?></div>
                </div>
            </div>
            <?php endforeach;?>
        </div>
        <?php endif;?>
    </div>
    </body>
</html>