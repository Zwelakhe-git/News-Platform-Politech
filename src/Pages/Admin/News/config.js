
export default {
    baseUrl: '',
    likeApi: '/api/v1/articles/{id}/likes',
    commentApi: '/api/v1/articles/{id}/comments',
    adminArticleApi: '/vkurse/user/author/admin',
    apiKey: 'vkurse_69cf890f379993_54239691',
    imgbbBaseUrl: 'https://api.imgbb.com/1/upload',
    imgbbApiKey: '7eeec47d1e4038de924efd0917fcb1e3',
    tinymceSmallEditor: ()=>{
        tinymce.init({
            selector: 'tinymce-small',
            height: 100,
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image media'
        });
    },
    tmpUploadApi: '/vkurse/api/v1/upload',
    tinymceFullEditor: ()=>{
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

            images_upload_handler: function (blobInfo, progress){
                return TmcUploadHandler(blobInfo, progress);
            },
            setup: function(editor){
                if(TmcContentHandler){
                    console.log("change handler for tinymce found");
                } else {
                    console.warn("no custom tmce content handler");
                }
                editor.on('change', TmcContentHandler);
            }
        })
    }
}