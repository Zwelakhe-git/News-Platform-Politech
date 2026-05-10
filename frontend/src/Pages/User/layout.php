<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title><?= $title ?? 'Профиль — spbVkurse'?></title>
    <?php if(isset($styles)){
            foreach($styles as $url){?>
            <link rel="stylesheet" href="<?= $url?>" />
    <?php } }
    if(isset($scripts)){
        foreach($scripts as $script){?>
        <script src="<?= $script['url']?>"
        <?php if(isset($script['params'])){
                foreach($script['params'] as $param => $value){?>
                <?= $param . "=\"$value\""?>
        <?php } }?> ></script><?php }?>
    <?php }?>
</head>

<body>
    <div id="root">
        <?php if(isset($template) && (file_exists(__DIR__ . "/$template") || file_exists($template))) require_once $template?>
    </div>
    <script>
        sessionStorage.setItem('user', JSON.stringify(<?= json_encode([
            'id' => $_SESSION['user']['id'],
            'full_name' => $_SESSION['user']['full_name']
        ])?>));
    </script>
</body>
</html>
