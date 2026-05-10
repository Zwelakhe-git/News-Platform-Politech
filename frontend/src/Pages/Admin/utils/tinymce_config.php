<?php
// admin/views/layout/tinymce_config.php
/**
 * this file is to be included in an environment where the config.php file is included.
 * it requires the TINE_API constant.
 */
?>
<script>
tinymce.init({
    selector: '.tinymce-editor',
    height: 500,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
        'autoresize'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic forecolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | link image media table | code | help | emoticons',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    
    // Кастомизация загрузки изображений
    file_picker_callback: function (callback, value, meta) {
        if (meta.filetype === 'image') {
            // Открываем модальное окно для загрузки изображений
            openImageManager(callback);
        }
    },
    
    // Автосохранение
    autosave_ask_before_unload: true,
    autosave_interval: '30s',
    autosave_prefix: 'tinymce-autosave-{path}{query}-{id}-',
    autosave_restore_when_empty: false,
    autosave_retention: '1440m',
    
    // Дополнительные настройки
    branding: false,
    elementpath: false,
    statusbar: true,
    promotion: false,
    
    // Поддержка русского языка
    //language: 'ru',
    //language_url: 'https://cdn.tiny.cloud/1/<?= TINY_API?>/tinymce/6/langs/ru.js',
    
    // Настройки для изображений
    image_advtab: true,
    image_dimensions: false,
    image_description: true,
    image_title: true,
    
    // Кастомизация диалоговых окон
    image_class_list: [
        {title: 'None', value: ''},
        {title: 'Responsive', value: 'img-fluid'},
        {title: 'Rounded', value: 'img-rounded'},
        {title: 'Circle', value: 'img-circle'},
        {title: 'Thumbnail', value: 'img-thumbnail'}
    ]
});

// Функция для открытия менеджера изображений
function openImageManager(callback) {
    // Создаем модальное окно
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'imageManagerModal';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Менеджер изображений</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="drag-drop-area" id="imageUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h5>Перетащите изображения сюда</h5>
                        <p>или нажмите для выбора файлов</p>
                        <input type="file" id="fileInput" multiple accept="image/*" style="display: none;">
                    </div>
                    <div id="uploadedImages" class="row mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Инициализируем Bootstrap модальное окно
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Обработчики для drag & drop
    const uploadArea = modal.querySelector('#imageUploadArea');
    const fileInput = modal.querySelector('#fileInput');
    
    uploadArea.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });
    
    // Drag & drop события
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        uploadArea.classList.add('drag-over');
    }
    
    function unhighlight() {
        uploadArea.classList.remove('drag-over');
    }
    
    uploadArea.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    });
    
    function handleFiles(files) {
        [...files].forEach(uploadFile);
    }
    
    function uploadFile(file) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('action', 'upload_image');
        
        // Показываем превью
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgContainer = document.createElement('div');
            imgContainer.className = 'col-md-3 mb-3';
            imgContainer.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                    <div class="card-body text-center">
                        <button type="button" class="btn btn-sm btn-primary select-image" data-url="${e.target.result}">
                            Выбрать
                        </button>
                    </div>
                </div>
            `;
            
            const uploadedImages = modal.querySelector('#uploadedImages');
            uploadedImages.appendChild(imgContainer);
            
            // Обработчик выбора изображения
            imgContainer.querySelector('.select-image').addEventListener('click', function() {
                const imageUrl = this.getAttribute('data-url');
                callback(imageUrl, {alt: file.name});
                bsModal.hide();
                modal.remove();
            });
        };
        reader.readAsDataURL(file);
        
        // Реальная загрузка на сервер (если нужно)
        fetch('/admin/upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Image uploaded:', data.url);
            }
        });
    }
    
    // Загружаем уже существующие изображения
    loadExistingImages();
    
    function loadExistingImages() {
        fetch('/admin/api.php?action=get_images')
            .then(response => response.json())
            .then(images => {
            	console.log("tinymce-get existing images");
                const uploadedImages = modal.querySelector('#uploadedImages');
                images.forEach(image => {
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'col-md-3 mb-3';
                    imgContainer.innerHTML = `
                        <div class="card">
                            <img src="${image.url}" 
                                 class="card-img-top" 
                                 style="height: 150px; object-fit: cover;">
                            <div class="card-body text-center">
                                <button type="button" class="btn btn-sm btn-primary select-image" 
                                        data-url="${image.url}">
                                    Выбрать
                                </button>
                            </div>
                        </div>
                    `;
                    
                    uploadedImages.appendChild(imgContainer);
                    
                    // Обработчик выбора изображения
                    imgContainer.querySelector('.select-image').addEventListener('click', function() {
                        const imageUrl = this.getAttribute('data-url');
                        callback(imageUrl, {alt: image.filename});
                        bsModal.hide();
                        modal.remove();
                    });
                });
            });
    }
    
    // Удаляем модальное окно при закрытии
    modal.addEventListener('hidden.bs.modal', function() {
        modal.remove();
    });
}
</script>