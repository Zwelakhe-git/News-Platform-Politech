<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title id="page-title"></title>
        <link rel="stylesheet" href="/vkurse/static/css/global.css"/>
        <link rel="stylesheet" href="/vkurse/static/css/html-defaults.css"/>
        <link rel="stylesheet" href="/vkurse/static/css/box-container.css"/>
        <link rel="stylesheet" href="/vkurse/static/css/adaptive-theme.css"/>
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
        <?php
        if(isset($styles)){
            foreach($styles as $url){?>
        <link rel="stylesheet" href="<?= $url?>" />
        <?php if(isset($scripts)){
        foreach($scripts as $url){?>
        <script defer src="<?= $url?>"></script>
        <?php } } } }?>
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
        <div id="top-bar" class="box">
            <div class="top-bar-pop-up">
                <div class="logo-text"><span>spb<em>Vkurse</em></span></div>
                <!-- <img id="logo" alt="logo"/> -->
                <div class="icon-box close-btn ">
                    <i class="fa-solid fa-x"></i>
                </div>
                <div class="search-bar">
                    <div class="text-area"><input type="text" name="search" placeholder="поиск"/></div>
                    <div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                </div>
            </div>
            <div class="top-bar-frame">
                <div class="tb-icons"></div>
                <header class="sticky top-0 z-50 flex min-h-[var(--vkurse-header-height)] items-center justify-between bg-header px-4 py-3">
                    <button class="p-2 -ml-2">
                        <Search class="w-6 h-6 text-header-foreground" />
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-md bg-header-foreground flex items-center justify-center">
                        <span class="text-header text-xs font-bold">✦</span>
                        </div>
                        <span class="text-header-foreground font-bold text-xl tracking-tight">VKURSE</span>
                    </div>
                    <button class="p-2 -mr-2">
                        <User class="w-6 h-6 text-header-foreground" />
                    </button>
                </header>
                <div
                    class="fixed left-1/2 z-40 w-full max-w-lg px-4 py-3 bg-card transition-transform duration-300 ease-in-out"
                    style={{
                        top: "var(--vkurse-header-height)",
                        transform: isVisible ? "translateX(-50%) translateY(15%)" : "translateX(-50%) translateY(-100%)",
                    }}
                    >
                    {/*<div class="border-b border-border bg-card py-2">*/}
                        <div class="flex gap-1 overflow-x-auto hide-scrollbar">
                        {tabs.map((tab) => (
                            <button
                            key={tab}
                            onClick={() => onTabChange(tab)}
                            className={`whitespace-nowrap px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 ${
                                activeTab === tab
                                ? "bg-foreground text-card"
                                : "bg-transparent text-foreground hover:bg-secondary"
                            }`}
                            >
                            {tab}
                            </button>
                        ))}
                        </div>
                </div>
            </div>
        </div>

        <main id="main">
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

                let np = document.querySelector('#nav-panel');
                let topBar = document.querySelector('#top-bar');
                let topBarPopUp = topBar.querySelector('.top-bar-pop-up');
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
                            topBarPopUp.style.setProperty('--pos', '0%');
                        }
                    });
                });

                topBarPopUp.querySelector('.close-btn')?.addEventListener('click', function(){
                    topBarPopUp.style.removeProperty('--pos');
                });
                
            </script>
            <div id="root">
                <div id="main-content">
                    <!-- do require -->
                    <?php isset($template) && require_once $template; ?>
                </div>
                <div id="right-panel">
                    <!-- do require -->
                    <?php require_once __DIR__ . '/right-panel-content.php'?>
                </div>
                <script>
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
                </script>
            </div>
        </main>
    </body>
    <!--<footer class="grid"></footer>-->
</html>