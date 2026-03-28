<?php
require_once __DIR__ . '/../../models/AdminModel.php';
require_once __DIR__ . '/../../config/config.php';

if (isset($_SESSION['name']) && $_SESSION['name'] != ADMIN_NAME) {
    try{
        $adminmodel = new AdminModel();
        $current_user = $adminmodel->getUserDetails($_SESSION['name']);
        if($current_user['name'] == 'Guest' && empty($current_user['email'])){
            //logMessage('header: failed to get user data');
        }
    } catch(Exception $e){
        logMessage($e->getMessage());
    }
} else {
    //logMessage('header: session array not set', 'error');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/media/images/favicon.png"/>
    <title></title>
    <!-- В секции head добавить: -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/views/layout/css/header.css">
    <link rel="stylesheet" href="/admin/views/layout/css/global.css">
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/<?= TINY_API?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script type="module" src="/admin/views/layout/js/script2.js"></script>
    <script src="/admin/views/layout/js/js-6966390.js"></script>
    <script src="/experimental/JS/vanish-on-scroll-panel.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg ">
        <div class="container">
                <?php if($_SESSION['name'] == ADMIN_NAME){
                    echo "<a class='navbar-brand' href='?action=dashboard'>
                    <i class='fas fa-cogs me-2'></i>Panel admen</a>";
                } else {?>
            		<div class="header" style="
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            margin: 10px 0px;
                            font-weight: 600;
                            font-size: 22px;">
            			<div class="info">Menu</div>
                        <div class="opts-container" onclick="openProfileModal('profileForm')"><i class="fas fa-cog me-2"></i></div>
            		</div>
                    <div class="user-info profile">
                        <div class="menu-panel"></div>
                        <div class="user-avatar" onclick="openProfileModal('avatar-edit-form')">
                            <?php if(!empty($current_user['avatar_url'])): ?>
                                <img src="<?php echo htmlspecialchars($current_user['avatar_url']); ?>" alt="User Avatar">
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <?php echo strtoupper(substr($current_user['name'] ?? 'G', 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="user-details">
                            <span class="user-name"><?php echo htmlspecialchars($current_user['name']); ?></span>
                            <?php if(!empty($current_user['email'])): ?>
                                <span class="user-email"><?php echo htmlspecialchars($current_user['email']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php }?>
            
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!--collapse-->
            <div class="navbar-collapse collapse nav-panel" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">
							<i class="fa-solid fa-house"></i><span class='link-text'>konektem</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'dashboard' ? 'active' : '' ?>" href="?action=dashboard">
                            <i class="fas fa-tachometer-alt me-1"></i><span class='link-text'>Dachbod</span>
                        </a>
                    </li>
                    <?php if($_SESSION['role'] == 'admin'){ ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'news' ? 'active' : '' ?>" href="?action=news">
                            <i class="fas fa-newspaper me-1"></i><span class='link-text'>Nouvel</span>
                        </a>
                    </li>
                    <?php }?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'music' ? 'active' : '' ?>" href="?action=music">
                            <i class="fas fa-music me-1"></i><span class='link-text'>Mizik</span>
                        </a>
                    </li>
                    
                    <?php if($_SESSION['role'] == 'admin'){ ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'services' ? 'active' : '' ?>" href="?action=services">
                            <i class="fas fa-concierge-bell me-1"></i><span class='link-text'>Sevis</span>
                        </a>
                    </li>
                    <?php }?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'events' ? 'active' : '' ?>" href="?action=events">
                            <i class="fas fa-calendar-alt me-1"></i><span class='link-text'>Eveneman</span>
                        </a>
                    </li>
                    
                    <?php if($_SESSION['role'] == 'admin'){ ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'interview' ? 'active' : '' ?>" href="?action=interview">
                            <i class="fas fa-microphone me-1"></i><span class='link-text'>Entèvyou</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'livestream' ? 'active' : '' ?>" href="?action=livestream">
                            <i class="fas fa-broadcast-tower me-1"></i><span class='link-text'>Strimin</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'orders' ? 'active' : '' ?>" href="?action=orders">
                            <i class="fas fa-regular fa-book"></i><span class='link-text'>orders</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'service_orders' ? 'active' : '' ?>" href="?action=service_orders">
                            <i class="fas fa-regular fa-book"></i><span class='link-text'>service orders</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'partners' ? 'active' : '' ?>" href="?action=partners">
                            <i class="fa-regular fa-handshake"></i><span class='link-text'>Patnè</span>
                        </a>
                    </li>
                    <?php }?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['action'] ?? '') == 'books' ? 'active' : '' ?>" href="?action=books">
                            <i class="fa-sharp fa-solid fa-book-open"></i><span class='link-text'>liv</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link logout-link" href="/admin/logout.php" style='display: none'>
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav dropdown-container">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['name']) ?>
                        </a>
                        <ul class="dropdown-menu" style="background-color: black">
                            <li><a class="dropdown-item" <?= $_SESSION['role'] == 'admin' ? 'href="?action=settings&method=index"' : 'onclick="openProfileModal(\'profileForm\')"'?>><i class="fas fa-cog me-2"></i>Paramet</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/admin/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Soti</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
    