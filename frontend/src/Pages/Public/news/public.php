<?php
//require_once __DIR__ . '/layout.php';
?>
<?php
/**
 * header slides
 */
?>

<div id="head" class="container-outer pad-20 white-bg bdr-box">
    <div id="news-slides">
<?php
$maxNewsSlides = 10;
$i = 0;
$slides = array_filter($news, function($item){
    return $item['position'] === 'mpnews_slide';
});
if (!$slides || empty($slides)){
?>
    <div>
        <h1>News Coming Soon</h1>
        <p>Оставайтесь на связи на спбВКурсе</p>
    </div>
<?php
} else {
foreach($slides as $article){
    if($i >= $maxNewsSlides) break;
    
?>
    <div class="slide-image-container full-w-h" data-id="<?= $article['id'] ?>">
        <img alt="news image"  src="<?= $article['image_location'] ?>" class="slide-image cvr full-w" />
        <p class="slide-img-desc"><?= $article['newsTitle'] ?></p>
    </div>
<?php
    ++$i;
}
?>
    </div>
    <div class="scroll-btn right">
        <ion-icon name='arrow-forward-outline' class='icon'></ion-icon>
    </div>
    <div class="scroll-btn left">
        <ion-icon name='arrow-back-outline' class='icon'></ion-icon>
    </div>
    <a href="/?p=actuality&id=<?= array_slice($slides, 0, 1, false)[0]['id'] ?>" id="slide-link" class="link no-dec">
        <div class="more-actions beep-anim">
            <span>Wè Plis</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
        </div>
    </a>
</div>
<?php }?>
<?php
/**
 * fading news
 */
$maxFadeNews = 20;
$i = 0;
$articles = array_filter($news, function($item){
    $match = $item['position'] === 'mpnews_fade';
    return $match;
});
?>
<div id="last-news" class="shelf-anim margin-rl-1 container-outer pad-20 white-bg bdr-box">
    <div class="section-info">
      <a href="/?p=actuality">
        <h1>DÈNYE NOUVÈL</h1>
      </a>
    </div>
    <div class="container-inner full-w" style="padding-top: 0px;padding-left:0px;padding-right:0px">
    	<div class="articles white-bg bdr-box">
<?php
if (!$articles || empty($articles)){
?>
    <div>
        <h1>News Coming Soon</h1>
        <p>Оставайтесь на связи на спбВКурсе</p>
    </div>
<?php
} else {
foreach($articles as $article){
    
    if($i >= $maxFadeNews) break;
?>
    <div class='artcl-itm full-wh' data-newsid="<?= $article['id'] ?>">
        <div class='artcl-cont full-wh'>
          <div class='artcl-info full-wh'>
            <a href="/?p=actuality&id=<?= $article['id'] ?>" class='img-link'>
                <div class='artcl-img'>
                <img alt='article img' class="full-wh" src='<?= $article['image_location'] ?>'/>
              </div>
            </a>
            <div class="artcl-text flx-disp col space-btwn">
              <div class="artcl-text-content elipsis no-ovrflw">
              <span class="line">
              	<b class="break-word"><?= $article['newsTitle'] ?></b> <?= $article['newsHeadline'] ?>
              </span>
              </div>
              <div class="artcl-media-stats f1-s f6-b flx-disp row">
              	    <div class="artcl-date">
                        <i class="fa-regular fa-clock"></i>
                        <span><?= $article['newsDate'] ?></span>
                    </div>
                    <!--<div class="artcl-read">
                        <i class="fa-regular fa-eye" data-itemid="<?= $article['id'] ?>" ></i>
                        <span><?= $article['reads'] ?? 0 ?></span>
                    </div>-->
                    <div class="artcl-like like-btn">
                        <i class="fa-regular fa-heart media-ico" data-itemname="actuality" data-itemid="<?= $article['id'] ?>"></i>
                        <span><?= $article['likes'] ?? 0 ?></span>
                    </div>
                    <div class="artcl-share share-btn">
                        <i class="fa-regular fa-paper-plane media-ico" data-itemname="actuality" data-itemid="<?= $article['id'] ?>"></i>
                        <span><?= $article['shares'] ?? 0 ?></span>
                    </div>
              </div>
            </div>
          </div>
        </div>
    </div>
<?php    
}

?>
            <div id="shareMenu" class="share-menu">
                <a id="shareVK" class="share-item">ВКонтакте</a>
                <a id="shareTG" class="share-item">Telegram</a>
                <a id="shareWA" class="share-item">WhatsApp</a>
            </div>
        </div>
        <a id='fade-news-link' href="/?p=actuality&id=<?= array_slice($articles, 0, 1, false)[0]['id'] ?>" class='slide-link' style="padding-left: 15px">
            <div class="more-actions beep-anim">
                <span>Wè Plis</span>
                <ion-icon name="arrow-forward-outline"></ion-icon>
            </div>
        </a>
    </div>
</div>
<?php }?>
<?php
/**
 * interviews section
 */
$maxInterviews = 20;
$i = 0;
?>
<div class="intrvw-cont shelf-anim margin-rl-1 container-outer pad-20 white-bg bdr-box">
    <div class="section-info">
      <a>
      	<h1>ENTÈVYOU</h1>
      </a>
    </div>
    <div class="container-inner full-wh" style="padding-top: 0px;padding-left:0px;padding-right:0px">
    	<div id="intrvws-list" data-itempage="interviews" class="intrvws no-ovrflw white-bg flx-disp row bdr-box">
<?php
if (!$interviews || empty($interviews)){
?>
    <div>
        <h1>News Coming Soon</h1>
        <p>Оставайтесь на связи на спбВКурсе</p>
    </div>
<?php
} else {
foreach($interviews as $interview){

?>
            <div class='intrvw-itm no-shrink full-wh' data-itemid="<?= $interview['id'] ?>">
                <div class='full-wh' style="overflow: hidden">
                    <div class='full-wh flx-disp row bdr-8' style="background-color:#ededed">
                        <a class=''>
                            <div class='left-img bg-white pad-5 abs-img-cont'>
                                <img alt='image' class="full-wh bdr-8" src='<?= $interview['image_location'] ?>'/>
                            </div>
                        </a>
                        <div class="flx-disp space-btwn col intrvw-descrp no-ovrflw" >
                            <div class="elipsis">
                                <b class="break-word"><?= $interview['title']?></b>
                                <?= $interview['description'] ?>
                            </div>
                            <div class="intrvws-media-stats f1-s f6-b pad-5 flx-disp row bdr-top solid-bdr" style="background-color:#ededed;">
                                    <div class="gap-x">
                                        <i class="fa-regular fa-clock"></i>
                                        <span><?= str_split($interview['created_at'])[0] ?></span>
                                    </div>
                                    <!--<div class="gap-x read-btn plays-btn">
                                        <i class="fa-regular fa-eye media-ico" data-itemname="interviews" data-itemid="<?= $interview['id'] ?>" ></i>
                                        <span><?= $interview['views'] ?></span>
                                    </div>-->
                                    <div class="like-btn gap-x">
                                        <i class="fa-regular fa-heart media-ico" data-itemname="interviews" data-itemid="<?= $interview['id'] ?>"></i>
                                        <span><?= $interview['likes'] ?></span>
                                    </div>
                                    <div class="share-btn">
                                        <i class="fa-regular fa-paper-plane media-ico" data-itemname="interviews" data-itemid="<?= $interview['id'] ?>"></i>
                                        <span><?= $interview['shares'] ?></span>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php
}
?>
        </div>
        <div id="shareMenu" class="share-menu">
            <a id="shareVK" class="share-item">ВКонтакте</a>
        	<a id="shareTG" class="share-item">Telegram</a>
            <a id="shareWA" class="share-item">WhatsApp</a>
        </div>
        <a href="/?p=interviews&id=<?= $interviews[0]['id'] ?>" style="padding-left: 15px" class="slide-link">
          <div class="more-actions beep-anim">
            <span>Wè Plis</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
          </div>
        </a>
    </div>
</div>
<?php }?>
<?php
/**
 * events section
 */
?>

<div id="events-section" class="shelf-anim margin-rl-1 colDir container-outer pad-20 white-bg bdr-box">
    <div id="events-section-bg" class="full-h"></div>
    <div class="section-info">
        <a href="/?p=events"><h1>EVENEMAN</h1></a>
    </div>
    <div id="events-list" data-itempage="events" class="container-inner">
<?php
if (!$events || empty($events)){
?>
    <div>
        <h1>News Coming Soon</h1>
        <p>Оставайтесь на связи на спбВКурсе</p>
    </div>
<?php
} else {
foreach($events as $event){
?>
        <div class="event-card" data-itemid="<?= $event['id'] ?>">
            <div class="ev-container flx-disp col">
                <img src="<?= $event['image_location'] ?>" alt="<?= $event['title'] ?>" class="event-image grow" />
                <div class="event-content pad-5">
                    <div class="ticket-info">
                        <span class="ticket-price">Pri Tikè: \$<?= $event['price'] ?></span>
                    </div>
                    <a href="/?p=buytickets&f=event&id=<?= $event['id'] ?>" class="buy-btn">buy tickets</a>
                </div>
            </div>
            <div class="event-info flx-disp col full-h space-btwn events-description">
                <div class="no-ovrflw" style="height: 80%;">
                    <?php if($event['description']){
                        $event['description'];
                    } else {?>
                        <p class="elipsis txt-lines-12 break-word pad-r5 pad-t20">no description</p>
                    <?php }?>
                </div>
                <div class="intrvws-media-stats f1-s f6-b pad-5 flx-disp row bdr-top solid-bdr" style="background-color:#ededed;">
                            <div class="gap-x">
                                <ion-icon class="" name="time-outline"></ion-icon>
                                <span><?= $event['eventDate'] ?></span>
                            </div>
                            <!--<div class="gap-x read-btn plays-btn">
                                <i class="fa-regular fa-eye media-ico"
                                data-itemname="events"
                                data-itemid="<?= $event['id'] ?>" ></i>
                                <span><?= $event['views'] ?? 0 ?></span>
                            </div>-->
                            <div class="like-btn gap-x">
                                <i class="fa-regular fa-heart media-ico"
                                data-itemname="events"
                                data-itemid="<?= $event['id'] ?>"></i>
                                <span><?= $event['likes'] ?? 0 ?></span>
                            </div>
                            <div class="share-btn">
                                <i class="fa-regular fa-paper-plane media-ico"
                                data-itemname="events"
                                data-itemid="<?= $event['id'] ?>"></i>
                                <span><?= $event['shares'] ?? 0 ?></span>
                            </div>
                    </div>
            </div>
        </div>
<?php
}
?>
    </div>
    <a href="/?p=events" class='slide-link'>
        <div class="more-actions beep-anim">
            <span>Wè Plis</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
        </div>
    </a>
</div>
<?php }?>
<?php
/**
 * music section
 */
$maxMusicContent = 5;
$i = 0;
$tracks = array_filter($music, function($item){
    return $item['position'] === 'mainpage';
});
?>
<div id="middle-panel" class="shelf-anim margin-rl-1 colDir container-outer pad-20 white-bg bdr-box">
    <div class="section-info">
        <a href="/?p=music">
        <h1>
            PLEYLIS
        </h1>
        </a>
    </div>
    <div id="music-charts-video" class="flxDisp colDir container-inner">
    
    <div class="media-row-content charts">
        <!-- this space should be automatically filled in JS -->
        <div class="section-info charts-title">
            <h2>TOP CHARTS</h2>
        </div>
        <div class="audio-charts flxDisp">
<?php
if (!$tracks || empty($tracks)){
?>
    <div>
        <h1>News Coming Soon</h1>
        <p>Оставайтесь на связи на спбВКурсе</p>
    </div>
<?php
} else {
foreach($tracks as $track){
    if($i >= $maxMusicContent) break;
?>
            <div class="track"
                id="track-<?= $track['id'] ?>"
                data-trackid="<?= $track['id'] ?>" data-itemid="<?= $track['id'] ?>">
                <div class="track-img">
                    <img class="full-w-h" alt="track image" src="<?= $track['image_location'] ?>" />
                </div>
                <div class="music-info">
                    <div class="music-title"><?= $track['track_name'] ?></div>
                    <div class="music-artist"><?= $track['artist_name'] ?></div>
                    <div class="player-controls">
                        <div class="play-btn" data-trackid="<?= $track['id'] ?>" data-itemid="<?= $track['id'] ?>">
                            <i class="fas fa-play media-ico play"
                            data-trackid="<?= $track['id'] ?>" data-itemid="<?= $track['id'] ?>" 
                            <?= strlen($track['location']) > 0 && 'data-src="{$track[\'location\']}"' ?>
                            data-tracktitle=<?= $track['track_name'] ?>></i>
                        </div>
                        <div class="progress-bar">
                            <div class="progress track-<?= $track['id'] ?>"></div>
                        </div>
                        <div class="music-duration track-<?= $track['id'] ?>"></div>
                    </div>
                    <div class="media-actions">
                        <div class="action-btn download-btn">
                            <i class="fas fa-download media-ico"
                            data-itemname="music"
                            data-trackid="<?= $track['id'] ?>"
                            data-itemid="<?= $track['id'] ?>" 
                            data-tracktitle="<?= $track['track_name'] ?>"></i>
                            <span><?= $track['downloads'] ?></span>
                            <a id="dd-a<?= $track['id'] ?>" download="<?= $track['track_name'] ?>"
                            data-href="<?= $track['location'] ?>" style="display: none;"></a>
                        </div>
                        <div class="action-btn like-btn">
                            <i class="fa-regular fa-heart media-ico"
                            data-itemname="music"
                            data-trackid="<?= $track['id'] ?>"
                            data-itemid="<?= $track['id'] ?>"
                            data-tracktitle="<?= $track['track_name'] ?>"></i>
                            <span><?= $track['likes'] ?></span>
                        </div>
                        <!--<div class="action-btn plays-btn">
                            <ion-icon name="eye-outline" class="media-ico"
                            data-itemname="music"
                            data-trackid="<?= $track['id'] ?>"
                            data-itemid="<?= $track['id'] ?>"
                            <span><?= $track['plays'] ?></span>
                        </div>-->
                        <div class="action-btn share-btn">
                            <i class="fa-regular fa-paper-plane media-ico"
                            data-itemname="music" 
                            data-itemid="<?= $track['id'] ?>"
                            data-trackid="<?= $track['id'] ?>"></i>
                            <span><?= $track['shares'] ?? 0 ?></span>
                        </div>
                    </div>
                    <span style="font-size: 5px;position: absolute;right: 0px;"><?= $track['owner'] ?? '' ?></span>
                </div>                                            
            </div>
<?php
    ++$i;
}
?>

            </div>
            <a href="/?p=music">
                <div class="more-actions beep-anim">
                    <span>Wè Plis</span>
                    <ion-icon name="arrow-forward-outline"></ion-icon>
                </div>
            </a>
        </div>
    </div>
    <audio id="audio-player"></audio>
</div>
<?php }?>
<?php
/**
 * informative video
 */
?>
<div id="right-panel" class="shelf-anim margin-rl-1 container-outer pad-20 white-bg bdr-box">
    <div id="essentials-section">
    <div class="section-info">
        <a href="/?p=actuality"><h1>VIDEYO</h1></a>
    </div>
    <div id="link-portrait-video">
        <div class="vid-container">
            <!-- dynamic load YT =-video-->
            <div id="rp-vid-container" class="html5-vid"></div>
        </div>
    </div>
    </div>
</div>

<?php
/**
 * services panel. decided to exclude
 */
$addServicesToMainPage = false;
if($addServicesToMainPage){
?>
<div id="services-panel" class="shelf-anim margin-rl-1 container-outer pad-20 white-bg bdr-box">
    <div class="section-info">
        <!-- /?p=services -->
        <a href=""><h1>SEVIC</h1></a>
    </div>
    <!-- preparing containers for pasting -->
    <div class='services-container container-inner'>
        <div id="services-list" data-itempage="services" class="no-ovrflw flx-disp row pad-5">
<?php
if (!$services || empty($services)){
?>
    <div>
        <h1>News Coming Soon</h1>
        <p>Оставайтесь на связи на спбВКурсе</p>
    </div>
<?php
} else {
foreach($services as $service){
?>
            <div class="serv-container" data-itemid="<?= $service['id'] ?>">
                <div class="service-desc">
                    <span><?= $service['name'] ?></span>
                </div>
                <div class="service-img">
                    <div class="service-img-cont">
                        <img alt="service-img" src="<?= $service['image_location'] ?>"/>
                    </div>
                </div>
            </div>
<?php
    }
?>
        </div>
        <button class='svc-scroll-btn left' type='button'>
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button class='svc-scroll-btn right' type='button'>
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>
    <a href="/?p=services&id=<?= $services[0]['id'] ?>" class="slide-link">
        <div class="more-actions beep-anim">
            <span>Wè Plis</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
        </div>
    </a>
</div>
<?php
}
}
/**
 * books section
 */
if (!$books || empty($books)){
?>
    <div>
        <h1>News Coming Soon</h1>
        <p>Оставайтесь на связи на спбВКурсе</p>
    </div>
<?php
} else {
?>
<div id="services-panel" class="shelf-anim margin-rl-1 container-outer pad-20 white-bg bdr-box">
    <div class="section-info">
    <!-- /?p=services -->
    <a href=""><h1>LIV</h1></a>
    </div>
    <!-- preparing containers for pasting -->
    <div class='books-container container-inner'>
        <div id="books-list" data-itempage="books" class="no-ovrflw  full-w flx-disp row">
<?php
foreach($books as $book){
?>
            <div class="book-item no-shrink full-wh flx-disp space-btwn" data-itemid="<?= $book['id'] ?>">
                <div class="book-img" style="width: 48%;">
                    <div class="full-wh">
                        <img class="full-wh" alt="book-image" src="<?= $book['image_location'] ?>"
                        style="border-radius: 10px;"/>
                    </div>
                </div>
                <div class="book-info flx-disp col full-h space-btwn no-ovrflw events-description" style="width: 50%;">
                    <div class="no-ovrflw" style="height: 80%">
                        <div class="book-title" style="margin-bottom: 10px;">
                            <h3><?= $book['title'] ?></h3>
                        </div>
                        <div class="book-desc" style="overflow-y: auto; height: 100%">
                            <?= $book['description'] ?>
                        </div>
                    </div>
                    <div class="book-media-stats f1-s f6-b flx-disp pad-5 row bdr-top solid-bdr">
                        <div class="book-read">
                            <i class="fa-regular fa-eye" data-itemid="<?= $book['id'] ?>" ></i>
                            <span><?= $book['reads'] ?? 0 ?></span>
                        </div>
                        <div class="book-like -btn">
                            <!--<i class="fa-sharp-duotone fa-thin fa-link"></i>-->
                            <i class="fa-solid fa-link media-ico" data-itemname="books"
                                data-itemid="<?= $book['id'] ?>"></i>
                        </div>
                        <div class="book-share share-btn">
                            <i class="fa-regular fa-paper-plane media-ico" data-itemname="books"
                                data-itemid="<?= $book['id'] ?>"></i>
                            <span><?= $book['shares'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
            </div>
<?php
}
}
?>
        </div>
        <button class='scroll-btn left' type='button'>
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button class='scroll-btn right' type='button'>
            <i class="fa-solid fa-chevron-right"></i>
        </button>
        <a href="/?p=books&id=<?= $books[0]['id'] ?>" class="slide-link">
            <div class="more-actions beep-anim">
            <span>Wè Plis</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
            </div>
        </a>
    </div>
</div>

<?php
/**
 * partners panel
 */
?>
<div id="partners-panel" class="shelf-anim margin-rl-1 container-outer pad-20 white-bg bdr-box">
    <div class="section-info">
        <h1>PATNÈ NOU YO</h1>
    </div>
    <div id="partners-list" class="partners">
<?php
if (!$partners || empty($partners)){
?>
    <div>
        <h1>News Coming Soon</h1>
        <p>Оставайтесь на связи на спбВКурсе</p>
    </div>
<?php
} else {
    $i = 0;
foreach($partners as $partner){
?>
        <div class='partner-img-cont <?= $i < 2 && 'small' ?>'>
            <img src="<?= $partner['image_location'] ?>" <?= $i == 1 && "style='width: 63%; height: 63%'" ?>/>
        </div>
<?php
} ++$i;
?>
    </div>
</div>

<div class="bottom-slide container-outer" style="padding: 0px">
<?php
/**
 * bottom slides
 */
$imgUrls = [
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_4657.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_4658.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_4659.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_4656.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_4660.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_4661.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_4662.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_4664.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_4665.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_5227.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_5244.JPG.jpg"
    ],
    [
    "mime_type" => "image/jpeg",
    "src" => "/media/images/bottom/IMG_5245.JPG.jpg"
    ]
];
foreach($imgUrls as $slide){
?>
    <div class="slide-container">
        <img type="<?= $slide['mime_type'] ?>" src="<?= $slide['src'] ?>"/>
    </div>
<?php
}
?>
</div>
<?php }?>
<?php
/**
 * cookie form, though it shouldn't reside in the static file, but dynamicall added
 */
?>
<div class='cookie-form-contnent'>
    <div class='cookie-msg'>
        Nou pran angajman pou nou respekte vi prive w avèk pèmisyon w, nou itilize COOKIES ak lòt trasè ankò nan lide pou kontwole odyans nou yo, pataje sou rezo sosyal yo, pèsonalize kontni yo ak piblisite pèsonalize sou sèvis nou yo.
        <a class='wht-clr' href='https://www.konektem.net/privacyandlegalinfo'>Politique de confidentialité</a>
    </div>
    <div class='btns'>
        <button type='button' class='wht-bg' id='cookie-allow'>Accept</button>
        <button type='button' class='transp' id='cookie-deny'>Reject</button>
    </div>
</div>

<?php
/**
 * whatsapp widget
 */

$whatsappData = [
    "phoneNumber" => "7282143510",
    "welcomeMessage" => "Hello! I'm interested in your services.",
    "countryCodes" => [
        [ "code"  => "+1", "country" => "US" ],
        [ "code" => "+44", "country" => "UK" ],
        [ "code" => "+91", "country" => "IN" ],
        [ "code" => "+33", "country" => "FR" ],
        [ "code" => "+49", "country" => "DE" ],
        [ "code" => "+7",  "country" => "RU" ]
        // Add more country codes as needed
    ]
];
?>
<div class="whatsapp-widget">
    <div class="whatsapp-button" id="whatsappToggle">
        <i class="fab fa-whatsapp"></i>
    </div>
    
    <div class="whatsapp-popup" id="whatsappPopup">
        <div class="whatsapp-header">
            <h3>Contact Us on WhatsApp</h3>
            <button class="close-whatsapp" id="closeWhatsapp">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="whatsapp-content">
            <p>Send us a message directly on WhatsApp. We typically respond within minutes.</p>
            
            <div class="whatsapp-number">
                <select id="countryCode">
                    <?php foreach($whatsappData['countryCodes'] as $code){?>
                        <option value="<?= $code['code']?>" <?= $code['country'] === "US" ? 'selected' : ''?>>
                            <?= $code['code']?> (<?= $code['country']?>)
                        </option>
                    <?php } ?>
                </select>
                <input type="text" id="phoneNumber" placeholder="Phone number" value="<?= $whatsappData['phoneNumber'] ?>">
            </div>
            
            <div class="whatsapp-actions">
                <button class="whatsapp-btn whatsapp-primary" id="sendWhatsapp">
                    <i class="fab fa-whatsapp"></i> Send Message
                </button>
                <button class="whatsapp-btn whatsapp-secondary" id="cancelWhatsapp">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    /**
     * whatsapp handler
     */
    let whatsappData = <?= json_encode($whatsappData) ?>;
    // Get elements
    const whatsappToggle = document.getElementById('whatsappToggle');
    const whatsappPopup = document.getElementById('whatsappPopup');
    const closeWhatsapp = document.getElementById('closeWhatsapp');
    const sendWhatsapp = document.getElementById('sendWhatsapp');
    const cancelWhatsapp = document.getElementById('cancelWhatsapp');
    const countryCode = document.getElementById('countryCode');
    const phoneNumber = document.getElementById('phoneNumber');
    
    // Toggle WhatsApp popup
    if (whatsappToggle) {
        whatsappToggle.addEventListener('click', function() {
            whatsappPopup.style.display = 'flex';
        });
    }
    
    // Close WhatsApp popup
    if (closeWhatsapp) {
        closeWhatsapp.addEventListener('click', function() {
            whatsappPopup.style.display = 'none';
        });
    }
    
    // Cancel button
    if (cancelWhatsapp) {
        cancelWhatsapp.addEventListener('click', function() {
            whatsappPopup.style.display = 'none';
        });
    }
    
    // Send WhatsApp message
    if (sendWhatsapp) {
        sendWhatsapp.addEventListener('click', function() {
            const fullNumber = countryCode.value + phoneNumber.value.replace(/\D/g, '');
            const message = encodeURIComponent(whatsappData.welcomeMessage);
            const whatsappUrl = `https://wa.me/${fullNumber}?text=${message}`;
            
            // Open WhatsApp in a new tab
            window.open(whatsappUrl, '_blank');
            
            // Close the popup
            whatsappPopup.style.display = 'none';
        });
    }
</script>

<!-- END OF DOM -->
<!-- SCRIPTS -->
<script src="/static/js/index.js"></script>
<?php
//require_once __DIR__ . '/footer.php';
?>