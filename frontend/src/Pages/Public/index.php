<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title id="page-title"></title>
        <link rel="stylesheet" href="/vkurse/static/css/global.css"/>
        <link rel="stylesheet" href="/vkurse/static/css/html-defaults.css"/>
        <link rel="stylesheet" href="/vkurse/static/css/box-container.css"/>
        <!-- <link rel="stylesheet" href="/vkurse/static/css/adaptive-theme.css"/> -->
        <link rel="stylesheet" href="/vkurse/static/css/promotions.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- TODO: Update og:title to match your application name -->
        <meta property="og:title" content="Konektem App" />
        <meta property="og:description" content="Lovable Generated Project" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="https://lovable.dev/opengraph-image-p98pqg.png" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="@Lovable" />
        <meta name="twitter:image" content="https://lovable.dev/opengraph-image-p98pqg.png" />
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
        <style>
            .search-bar{
                display: flex;
                gap: 0px;
                width: 90%;
                margin: auto;
                align-items: center;
                border-radius: 10px;
                background-color: white;
                outline: 2px solid #ca3030;
                padding: 8px;
                justify-content: space-between;
            }
            .search-bar *{
                border-radius: inherit;
            }
            .text-area{
                width: 97%;
            }
            .text-area input{
                height: 100%;
                width: 100%;
                font-weight: bold;
                border: none;
                font-size: 18px;
                text-align: center;
            }
            .text-area input:focus{
                outline: none;
            }
            .search-icon{
                width: 30px;
                height: 100%;
            }
            .search-icon .icon{
                size: 100%;
            }
            .logo-text {
            text-align: center;
            margin-bottom: 1.75rem;
            }
            .logo-text span {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1a73e8;
            letter-spacing: -0.5px;
            }
            .logo-text span em {
            font-style: normal;
            color: #d93025;
            }
            #top-bar .close-btn{
                position: absolute;
                bottom: 5px;
            }
        </style>
    </head>
    <body class="">
        
        <div id="main">
            <div id="np-col" class="box">
                <div id="nav-panel" class="open">
                    <div id="nav-panel-header">
                        <div class="bars disabled">
                            <i class="fa-solid fa-bars bars--icon"></i>
                            <!--<div class="bars--icon"></div>-->
                        </div>
                        <div class="logo">
                            <div class="logo-text"><span>spb<em>Vkurse</em></span></div>
                            <!-- <img class="logo--img" width="100%" height="100%" alt="logo"/> -->
                        </div>
                    </div>
                    <nav class="nav">
                        <div class="nav-links" role="menu">
                            <a class="nav-link" role="menuitem">
                                <div class="nav-link-wrap">
                                    <div class="icon-bg"></div>
                                    <i class="fa-solid fa-house"></i>
                                </div>
                                <span class="nav-link-text">Новости</span>
                            </a>
                            <a class="nav-link">
                                <div class="nav-link-wrap">
                                    <div class="icon-bg"></div>
                                    <i class="fa-solid fa-newspaper"></i>
                                </div>
                                <span class="nav-link-text">Статьи</span>
                            </a>
                            <a class="nav-link search-btn">
                                <div class="nav-link-wrap">
                                    <div class="icon-bg"></div>
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </div>
                                <span class="nav-link-text">Поиск</span>
                            </a>
                            <a class="nav-link search-btn">
                                <div class="nav-link-wrap">
                                    <div class="icon-bg"></div>
                                    <i class="fa-solid fa-bell"></i>
                                </div>
                                <span class="nav-link-text">Подписки</span>
                            </a>
                            <a class="nav-link search-btn" href="/vkurse/user/me">
                                <div class="nav-link-wrap">
                                    <div class="icon-bg"></div>
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <span class="nav-link-text">Профиль</span>
                            </a>
                        </div>
                        
                    </nav>
                    <div id="nav-panel-footer">
                        <ul>
                            <li>
                                <i class="fa-solid fa-info"></i>
                                <a>о нас</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- <script>
                function getCSSVariable(element, varName){
                    return getComputedStyle(element).getPropertyValue(varName);
                }
                function setCSSVariable(element, varName, value){
                    element.style.setProperty(varName, value);
                }
                function removeCSSVariable(element, varName){
                    element.style.removeProperty(varName);
                }

                let np = document.querySelector('#nav-panel');
                let topBar = document.querySelector('#top-bar');
                //let topBarPopUp = topBar.querySelector('.top-bar-pop-up');
                let bars = document.querySelector('#nav-panel .bars');
                let npSyles = getComputedStyle(np);
                let searchBtns = document.querySelectorAll('.search-btn');
                let navPanelSearchBtn = np.querySelector('.search-btn')

                bars?.addEventListener('click', (e)=>{
                    if(!np.classList.contains('open')){
                        setCSSVariable(document.body, '--nav-panel-col-width', `${np.scrollWidth}px`);
                    } else {
                        removeCSSVariable(document.body, '--nav-panel-col-width');
                    }
                    np.classList.toggle('open');
                    
                });

                searchBtns.forEach(btn => {
                    btn.addEventListener('click', function(){
                        if(np.querySelector('.search-btn') === this){
                            //topBarPopUp.style.setProperty('--pos', '0%');
                        }
                    });
                });

                // topBarPopUp.querySelector('.close-btn')?.addEventListener('click', function(){
                //     topBarPopUp.style.removeProperty('--pos');
                // });
                
            </script> -->
            <div id="root">
                <div id="main-content">
                    <!-- do require -->
                    <?php (isset($template) && (file_exists(__DIR__ . "/$template") || file_exists($template))) && require_once $template; ?>
                </div>
                <!-- <script>
                    /**
                     * the template is designed in such a way that the top bar takes the first part of the height.
                     * the grid contains the main content and the right panel, allowing scroll on individual sections,
                     * so the scroll height will reach its maximum once the whole grid is in view.
                    */
                    let mainSection = document.querySelector('#main');
                    let mainSecRec = mainSection.getBoundingClientRect();
                    let topBarRec = topBar.getBoundingClientRect();
                    let topBarStyles = getComputedStyle(topBar);
                    let numberRegex = /\d+/;

                    function disableScrollWhileOnTop(){
                        //console.log(window.scrollY < (topBarRec.height - numberRegex.exec(topBarStyles.paddingTop)[0]));
                        if(window.scrollY < (topBarRec.height - numberRegex.exec(topBarStyles.paddingTop)[0])){
                            document.querySelector("#main-content").style.setProperty('overflow', 'hidden');
                            document.querySelector("#right-panel").style.setProperty('overflow', 'hidden');
                        } else {
                            document.querySelector("#main-content").style.removeProperty('overflow');
                            document.querySelector("#right-panel").style.removeProperty('overflow');
                        }
                    }
                    let fixedElements = [
                        document.querySelector('#nav-panel'),
                        document.querySelector('.right-panel-col')
                    ];
                    function keepFixedElementsFixed(){
                        let topBarHeight = getCSSVariable(document.body, '--top-bar-min-h');
                        //console.log(Math.max(0, (topBarRec.height - numberRegex.exec(topBarStyles.paddingTop)[0]) - window.scrollY))
                        setCSSVariable(document.body, '--fixed-el-top', `${Math.max(0, (topBarRec.height - numberRegex.exec(topBarStyles.paddingTop)[0]) - window.scrollY)}px`);
                    }
                    window.addEventListener('scroll', function(){
                        disableScrollWhileOnTop();
                        keepFixedElementsFixed();
                    });
                    disableScrollWhileOnTop();
                    keepFixedElementsFixed();
                </script> -->
            </div>
        </div>
    </body>
</html>