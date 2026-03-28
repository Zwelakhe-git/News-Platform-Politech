<?php 
require_once ADMIN_PATH . '/views/layout/header.php'; 
?>

<script src="https://cdn.tiny.cloud/1/<?= TINY_API?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<style>
    .tox-tinymce {
        border-radius: 8px;
        border: 1px solid #dee2e6 !important;
        margin-bottom: 20px;
    }
</style>

<h2>Redije nouvel</h2>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <!-- ВАШИ СУЩЕСТВУЮЩИЕ ПОЛЯ БЕЗ ИЗМЕНЕНИЙ -->
    <div class="mb-3">
        <label for="newsHeadline" class="form-label">News Headline *</label>
        <textarea type="text" class="form-control" id="newsHeadline" name="newsHeadline" required><?= htmlspecialchars($news['newsHeadline'])?></textarea>
    </div>
    
    <div class="mb-3">
        <label for="newsCategory" class="form-label">Category *</label>
        <select class="form-control" id="newsCategory" name='newsCategory'>
            <option value='politics' <?= $news['newsCategory'] === 'politics' ? 'selected' : ''?>>Politik</option>
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
        <script>
        	// dynamically select option
            const options = document.querySelectorAll("#newsCategory option");
            let selectedOpt = (Array.from(options)).find(el => el.value === '<?= $news['newsCategory']?>');
            if(selectedOpt){
                //console.log(document.querySelector(`#newsCategory option[value="${selectedOpt.value}"]`));
                document.querySelector(`#newsCategory option[value="${selectedOpt.value}"]`).selected = true;
            }
            
        </script>
    </div>
    
    <div class="mb-3">
        <label for="newsTitle" class="form-label">Tit nouvel *</label>
        <input type="text" class="form-control" id="newsTitle" name="newsTitle" 
               value="<?= htmlspecialchars($news['newsTitle'] ?? '') ?>" required>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="newsDate" class="form-label">Dat nouvel *</label>
                <input type="date" class="form-control" id="newsDate" name="newsDate" 
                       value="<?= $news['newsDate'] ?? '' ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="news_image" class="form-label">Imaj nouvel</label>
                <input type="file" class="form-control" id="news_image" name="news_image" accept="image/*">
                <?php if ($news['image_location']): ?>
                <div class="mt-2">
                    <p>Imaj aktyel:</p>
                    <img src="<?= $news['image_location'] ?>" width="200" class="img-thumbnail">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- ЕДИНСТВЕННОЕ ИЗМЕНЕНИЕ: добавляем класс tinymce-editor -->
    <div class="mb-3">
        <label for="fullContent" class="form-label">Full Content *</label>
        <textarea class="form-control tinymce-editor" id="fullContent" name="fullContent" required><?= $news['fullContent'] ?></textarea>
    </div>
    
    <!-- ВАШ СУЩЕСТВУЮЩИЙ ЧЕКБОКС БЕЗ ИЗМЕНЕНИЙ -->
    <div class="mb-3">
        <label for="cb-filter" class="form-label check">
            <input type="checkbox" class="form-control" id="cb-filter" name="mpContent"/>
            <span class="checkmark"></span>
            add to main page content
        </label>
        <div class="mb-3" style="display: none">
            <label for="mpitem-pos">select position</label>
            <select id="mpitem-pos" class="form-control" name="position">
                <option value="newsSlide" selected>Slide</option>
                <option value="fadeNews">Fade</option>
            </select>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">Renouvle imaj</button>
    <a href="?action=news" class="btn btn-secondary">Anile</a>
</form>

<script>
// ВАШ СУЩЕСТВУЮЩИЙ СКРИПТ БЕЗ ИЗМЕНЕНИЙ
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

// Показ/скрытие позиции (добавляем)
document.getElementById('cb-filter').addEventListener('change', function() {
    const positionDiv = document.querySelector('#mpitem-pos').parentNode;
    positionDiv.style.display = this.checked ? 'block' : 'none';
});
</script>
<?php require_once ADMIN_PATH . '/views/layout/tinymce-init.php'; ?>
<?php require_once ADMIN_PATH . '/views/layout/footer.php'; ?>