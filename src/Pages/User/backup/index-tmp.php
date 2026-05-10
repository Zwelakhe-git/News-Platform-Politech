<?php
// plan the navigation to make it comfortable to edit
$navlinks = [
    [
        'link-text' => 'Home',
        'url' => '/vkurse',
        'link-icon' => '<i class="fa-solid fa-house"></i>'
    ],
    [
        'link-text' => 'Dashboard',
        'url' => '/vkurse/user/me',
        'link-icon' => '<i class="fa-solid fa-gauge"></i>'
    ],
    [
        'link-text' => 'My Articles',
        'url' => '/vkurse/user/author/admin/create',
        'link-icon' => '<i class="fa-solid fa-newspaper"></i>'
    ],
    [
        'link-text' => 'Mail',
        'url' => '',
        'link-icon' => '<i class="fa-regular fa-envelope"></i>'
    ],
    [
        'link-text' => 'Notifications',
        'url' => '',
        'link-icon' => '<i class="fa-solid fa-bell"></i>'
    ],
    [
        'link-text' => 'Logout',
        'url' => '/vkurse/auth/logout',
        'link-icon' => '<i class="fas fa-sign-out-alt"></i>',
        'class' => 'logout-btn'
    ],
    [
        'link-text' => 'settings',
        'url' => '',
        'link-icon' => '<i class="fa-solid fa-gear"></i>'
    ]
];

$showRightPanel = false;

$showRightPanel &= (isset($user['subscription']) || $user['subscription'] !== 'premium')
                & ($action !== 'edit');
?>

<!DOCTYPE html>
<html>
    <head>
        <title id="page-title"></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/vkurse/static/css/profile.css">
        <link rel="stylesheet" href="/vkurse/static/css/global.css"/>
        <link rel="stylesheet" href="/vkurse/static/css/html-defaults.css"/>
        <link rel="stylesheet" href="/vkurse/static/css/box-container.css"/>
        <link rel="stylesheet" href="/vkurse/static/css/adaptive-theme.css"/>
        <link rel="stylesheet" href="/vkurse/static/css/profile-page.css"/>
        <link rel="stylesheet" href="/vkurse/static/css/profile.css">
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            .bars{
                display: none;
            }
            .nav-panel-ctrl .icon{
                transform: rotate(0deg);
                transition: transform 0.3s linear;
            }
            #nav-panel.open .nav-panel-ctrl .icon{
                transform: rotate(180deg);
            }
        </style>
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
    <body class="">
        <div id="top-bar" class="box">
            <div class="top-bar-pop-up">
                <img id="logo" alt="logo"/>
                <div class="icon-box close-btn ">
                    <i class="fa-solid fa-x"></i>
                </div>
            </div>
            <div class="top-bar-frame">
                <div>
                    text
                </div>
                <div class="profile-info box">
                    <div class="user-name"><?= $user['full_name']?></div>
                    <div class="avatar">
                        <div class="image-container active <?= $user['role'] === 'author' ? 'gold' : 'regular'?>">
                            <img src="<?= $user['avatar_url']?>" alt="avatar"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main id="main">
            <div id="np-col" class="box">
                <div id="nav-panel">
                    <div id="nav-panel-header">
                        <div class="bars">
                            <i class="fa-solid fa-bars bars--icon"></i>
                            <!--<div class="bars--icon"></div>-->
                        </div>
                        <div class="avatar">
                            <div class="image-container active <?= $user['role'] === 'author' ? 'gold' : 'regular'?>">
                                <img src="<?= $user['avatar_url']?>" alt="avatar"/>
                            </div>
                        </div>
                    </div>
                    <nav class="nav">
                        <div class="nav-links" role="menu">
                            <?php foreach($navlinks as $navlink){?>
                            <a class="nav-link" role="menuitem" href="<?= $navlink['url']?>">
                                <div class="nav-link-wrap">
                                    <div class="icon-bg"></div>
                                    <?= $navlink['link-icon']?>
                                </div>
                                <span class="nav-link-text"><?= $navlink['link-text']?></span>
                            </a>
                            <?php }?>
                        </div>
                        <script>
                            let logoutButtons = document.querySelectorAll('.logout-btn');
                            logoutButtons.forEach(btn => {
                                btn.addEventListener('click', ()=>{
                                    
                                });
                            });
                        </script>
                    </nav>
                    <div id="nav-panel-footer">
                        <ul>
                            <li class="footer-item theme-toggler icon-box" role="menuitem">
                                <div class="icon-wrap">
                                    <div class="icon-bg"></div>
                                    <i class="fa-solid fa-sun"></i>
                                </div>
                                <span>тема</span>
                            </li>
                            <li class="footer-item nav-panel-ctrl icon-box" role="menuitem">
                                <div class="icon-wrap">
                                    <div class="icon-bg"></div>
                                    <i class="fa-solid fa-right-long icon"></i>
                                </div>
                                <span></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <script>
                function getCSSVariable(element, varName){
                    return getComputedStyle(element).getPropertyValue(varName);
                }
                function setCSSVariable(element, varName, value){
                    element.style.setProperty(varName, value);
                }
                function removeCSSVariable(element, varName){
                    element.style.removeProperty(varName);
                }
                function navPanelControl(e){
                    if(!np.classList.contains('open')){
                        setCSSVariable(document.body, '--nav-panel-col-width', `${np.scrollWidth}px`);
                    } else {
                        removeCSSVariable(document.body, '--nav-panel-col-width');
                    }
                    np.classList.toggle('open');
                    
                }

                let np = document.querySelector('#nav-panel');
                let npCol = document.querySelector('#np-col');
                let npController = document.querySelector('.nav-panel-ctrl');
                let topBar = document.querySelector('#top-bar');
                let topBarPopUp = topBar.querySelector('.top-bar-pop-up');
                let bars = document.querySelector('#nav-panel .bars');
                let npSyles = getComputedStyle(np);
                let themeToggler = document.querySelector('.theme-toggler');
                let searchBtns = document.querySelectorAll('.search-btn');
                let navPanelSearchBtn = np.querySelector('.search-btn')
                let topBarRec = topBar.getBoundingClientRect();

                bars?.addEventListener('click', navPanelControl);
                npController?.addEventListener('click', navPanelControl);
                
                themeToggler?.addEventListener('click', function(e){
                    let darkTheme = document.body.classList.contains('dark-theme');
                    let icon = this.querySelector('i');
                    if(darkTheme){
                        icon.classList.add('fa-solid');
                        icon.classList.remove('fa-regular');
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                    }
                    document.body.classList.toggle('dark-theme');
                });

                searchBtns.forEach(btn => {
                    btn.addEventListener('click', function(){
                        if(np.querySelector('.search-btn') === this){
                            topBarPopUp.style.setProperty('--pos', '0%');
                        }
                    });
                });

                topBarPopUp.querySelector('.close-btn')?.addEventListener('click', function(){
                    topBarPopUp.style.removeProperty('--pos');
                });
                //npCol.style.setProperty('top', topBarRec.height + 'px');
            </script>
            <div id="root">
                <div id="main-content">
                    <!-- do require -->
                    <?php if(isset($template)) require_once $template; ?>
                </div>
                <?php if($showRightPanel){?>
                <div id="right-panel">
                    <!-- do require -->
                    <?php require_once __DIR__ . '/../right-panel-content.php'?>
                </div>
                <?php }?>
                <script>
                    /**
                     * the template is designed in such a way that the top bar takes the first part of the height.
                     * the grid contains the main content and the right panel, allowing scroll on individual sections,
                     * so the scroll height will reach its maximum once the whole grid is in view.
                    */
                    let mainSection = document.querySelector('#main');
                    let mainSecRec = mainSection.getBoundingClientRect();
                    let topBarStyles = getComputedStyle(topBar);
                    let numberRegex = /\d+/;
                    
                </script>
            </div>
        </main>
    </body>
</html>