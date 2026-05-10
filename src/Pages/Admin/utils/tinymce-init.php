
<script>
tinymce.init({
    selector: '.tinymce-editor',
    height: 500,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic forecolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | link image media table | code | help',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
    relative_urls: false,
    remove_script_host: false,
    convert_urls: false,

    // Используем ваш существующий FileUploader через upload.php
    images_upload_handler: function (blobInfo, progress) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/admin/upload.php');

            xhr.upload.onprogress = function (e) {
                progress(e.loaded / e.total * 100);
            };

            xhr.onload = function () {
                if (xhr.status === 403) {
                    reject({ message: 'Not authorized', remove: true });
                    return;
                }

                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }

                const json = JSON.parse(xhr.responseText);

                if (!json.success) {
                    reject(json.message);
                    return;
                }

                // Используем относительный путь, который возвращает FileUploader
                console.log("tinymce-init: url: " + json.url);
                resolve(json.url);
            };

            xhr.onerror = function () {
                reject('Image upload failed due to XHR error');
            };

            const formData = new FormData();
            formData.append('image', blobInfo.blob(), blobInfo.filename());

            xhr.send(formData);
        });
    },

    // Русский язык (опционально)
    //language: 'ru',
    //language_url: 'https://cdn.tiny.cloud/1/<?= TINY_API?>/tinymce/6/langs/ru.js',

    // Дополнительные настройки
    branding: false,
    promotion: false,

    // Автосохранение
    autosave_ask_before_unload: true,
    autosave_interval: '30s',
    autosave_prefix: 'tinymce-autosave-{path}{query}-{id}-',
    autosave_restore_when_empty: false,
    autosave_retention: '1440m'
});
</script>