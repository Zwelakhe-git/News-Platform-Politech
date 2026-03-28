<?php 
//require_once ADMIN_DIR . '/views/layout/header.php';
//require_once ADMIN_PATH . '/config/logMessage.php';
?>

<!-- Добавляем TinyMCE -->
<script src="https://cdn.tiny.cloud/1/<?= TINY_API?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<style>
    .tox-tinymce {
        border-radius: 8px;
        border: 1px solid #dee2e6 !important;
        margin-bottom: 20px;
    }
</style>

<h2>Ajoute nouvel</h2>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form id="newsForm" method="POST" enctype="multipart/form-data">
    <!-- ВСЕ ВАШИ СУЩЕСТВУЮЩИЕ ПОЛЯ БЕЗ ИЗМЕНЕНИЙ -->
    <div class="mb-3">
        <label for="newsHeadline" class="form-label">News Headline <span class="required">*</span></label>
        <textarea type="text" class="form-control" id="newsHeadline" name="newsHeadline" required></textarea>
    </div>
    
    <div class="mb-3">
        <label for="newsCategory" class="form-label">Category <span class="required">*</span></label>
        <select class="form-control" id="newsCategory" name='newsCategory'>
            <option value='politics'>Politik</option>
            <option value='security'>Sekirite</option>
            <option value='society'>Sosyete</option>
            <option value='diplomatie'>Diplomasi</option>
            <option value='international'>Entènasyonal</option>
            <option value='economy'>Ekonomi</option>
            <option value='finance'>Finans & Envestisman</option>
            <option value='entertainment'>Divètisman</option>
            <option value='environment'>Anviwònman</option>
            <option value='family'>Fanmi</option>
            <option value='culture'>Kilti</option>
            <option value='music & video'>Mizik e Videyo</option>
            <option value='cinema'>Sinema</option>
            <option value='mode'>Mòd & Estil</option>
            <option value='personality'>Pèsonalite</option>
            <option value='technology'>Teknoloji</option>
            <option value='kitchen'>Kizin & Resèt</option>
            <option value='trip'>Vwayaj</option>
            <option value='health'>Sante</option>
            <option value='sport'>Espò</option>
            <option value='education'>Edikasyon</option>
            <option value='religion'>Relijyon</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="newsTitle" class="form-label">Tit Nouvel <span class="required">*</span></label>
        <input type="text" class="form-control" id="newsTitle" name="newsTitle" required>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="newsDate" class="form-label">Dat nouvel <span class="required">*</span></label>
                <input type="date" class="form-control" id="newsDate" name="newsDate" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="news_image" class="form-label">Imaj nouvel (drag and drop)</label>
                <input type="file" class="form-control" data-preview="news-prev-img" id="news_image" name="news_image" accept="image/*">
                <img id="news-prev-img" class="img-thumbnail" style="display:none;margin-top: 10px; width: 150px; height: 150px"/>
            </div>
        </div>
    </div>
    
    <!-- ЕДИНСТВЕННОЕ ИЗМЕНЕНИЕ: добавляем класс tinymce-editor к textarea -->
    <div class="mb-3">
        <label for="fullContent" class="form-label">Full Content <span class="required">*</span></label>
        <textarea class="form-control tinymce-editor" id="fullContent" name="fullContent"></textarea>
    </div>
    
    <!-- ВАШИ СУЩЕСТВУЮЩИЕ ЧЕКБОКСЫ БЕЗ ИЗМЕНЕНИЙ -->
    <div class="mb-3">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="draft">
                    <input type="radio" class="" id="draft" name="article-status">
                    Save As Draft
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="publish">
                    <input type="radio" class="" id="publish" name="article-status">
                    Publish
                </label>
            </div>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">Ajoute nouvel</button>
    <a href="/user/me/articles" class="btn btn-secondary">Anile</a>
</form>

<script>
// ВАШ СУЩЕСТВУЮЩИЙ СКРИПТ БЕЗ ИЗМЕНЕНИЙ
document.getElementById('newsDate').value = new Date().toISOString().split('T')[0];
newsCategory = document.querySelector("select[id='newsCategory']");
if(newsCategory){
    newsCategory.addEventListener("change", function(){
        if(newsCategory.value == "new"){
            let inpElem = document.querySelector("input[id='newsCategory']");
            if(inpElem){
                inpElem.style.display = "block";
                newsCategory.style.display = "none";
            }
        }
    })
}

    document.querySelector('#newsForm').addEventListener('submit', function(e) {
        const content = tinymce.get('fullContent').getContent();
        if (!content.trim()) {
            e.preventDefault();
            alert('Please enter content for the news article');
            tinymce.get('fullContent').focus();
            return false;
        }
    });
// Показ/скрытие позиции (добавляем к существующему)
document.getElementById('cb-filter').addEventListener('change', function() {
    const positionDiv = document.querySelector('#mpitem-pos').parentNode;
    positionDiv.style.display = this.checked ? 'block' : 'none';
});
</script>
<?php require_once ADMIN_DIR . '/views/layout/tinymce-init.php'; ?>

<?php //require_once ADMIN_DIR . '/views/layout/footer.php'; ?>