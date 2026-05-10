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
            <span class="badge badge--sub" id="badgeSub" style="display: none">Premium</span>
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
    <?php require_once isset($section) ?  $section : 'index.php'; ?>
</main>

<script>
    window.currentRole = "<?= $_SESSION['user']['role']?>";
    window.hasSub = <?= isset($_SESSION['user']['subscription'])?>;
</script>
<script src="<?= BASE_URL . '/static/js/profile-mert.js'?>"></script>