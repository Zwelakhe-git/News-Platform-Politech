<?php
// news data
?>

<div class='container' id='current-news-root'></div>
    <div class="container all-N">
    <!-- <div class="breaking-news">
        <h2><i class="fas fa-bolt"></i> BREAKING NEWS</h2>
        <a href='#'><p class='wht-clr'>TOP STORY</p></a>
    </div> -->

    
    <?php
        $newsCategories = [
            [
                'name' => 'technology',
                'id' => 'technology',
                'text' => 'technology'
            ],
            [
                'name' => 'business',
                'id' => 'business',
                'text' => 'business'
            ],
            [
                'name' => 'politics',
                'id' => 'politics',
                'text' => 'politics'
            ],
            [
                'name' => 'entertainment',
                'id' => 'entertainment',
                'text' => 'entertainment'
            ],
            [
                'name' => 'sports',
                'id' => 'sports',
                'text' => 'sports'
            ],
            [
                'name' => 'health',
                'id' => 'health',
                'text' => 'health'
            ],
            [
                'name' => 'science',
                'id' => 'science',
                'text' => 'science'
            ]
        ];

        foreach($newsCategories as $cat){
            $targetArticles = array_filter($news, fn($article) => $article['category'] === $cat['name']);
    ?>
    <section id="<?= $cat['id']?>" class="news-section">
        <div class="section-header">
            <h2 class="section-title"><?= $cat['text']?></h2>
            <a href="#" class="view-all">View All</a>
        </div>
        <div class="news-grid">
            <?php
                foreach($targetArticles as $article){
            ?>
            <div class="news-card" id="<?= $article['id'] ?>">
                <div class="news-img">
                    <img src="<?= $article['cover_image_url'] ?? $article['urlToImage'] ?>" alt="<?= $cat['name']?> news">
                </div>
                <div class="news-content">
                    <div class="author">by: <?= $article['author'] ?? 'unknown'?></div>
                    <div class="news-date"><?= $article['published_at'] ?? $article['publishedAt'] ?></div>
                    <h3 class="news-title"><?= $article['title'] ?></h3>
                    <p class="news-excerpt"><?= $article['excerpt'] ?? $article['description'] ?></p>
                    <?php if(isset($article['source']) && $article['source'] !== 'local'){
                    ?><a href="<?= $article['url']?>">source</a>
                    <?php } ?>
                    <div class="full-content">
                        <?= $article['content'] ?>
                    </div>
                    <a class="read-more" data-state="more">Read More <i class="fas fa-chevron-down"></i></a>
                </div>
            </div>
            <?php }?>
        </div>
    </section>
    <?php }?>
</div>
<script>
    // Smooth scrolling for section navigation
    document.querySelectorAll('.section-nav a').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            window.scrollTo({
                top: targetSection.offsetTop - 70,
                behavior: 'smooth'
            });
            
            // Update active class
            document.querySelectorAll('.section-nav a').forEach(a => a.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Update active nav link based on scroll position
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('.news-section');
        const navLinks = document.querySelectorAll('.section-nav a');
        
        let currentSection = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (pageYOffset >= (sectionTop - 100)) {
                currentSection = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + currentSection) {
                link.classList.add('active');
            }
        });
    });
    
    // Read More functionality
    document.querySelectorAll('.read-more').forEach(button => {
        button.addEventListener('click', function() {
            const fullContent = this.previousElementSibling;
            const excerpt = fullContent.previousElementSibling;
            
            if (this.getAttribute('data-state') === 'more') {
                // Expand content
                fullContent.classList.add('expanded');
                excerpt.classList.add('expanded');
                this.innerHTML = 'Read Less <i class="fas fa-chevron-up"></i>';
                this.setAttribute('data-state', 'less');
            } else {
                // Collapse content
                fullContent.classList.remove('expanded');
                excerpt.classList.remove('expanded');
                this.innerHTML = 'Read More <i class="fas fa-chevron-down"></i>';
                this.setAttribute('data-state', 'more');
            }
        });
    });
</script>
<!-- one signal initialization -->
<script type="module" src="/konektem/static/js/onesignal-init.js"></script>
    