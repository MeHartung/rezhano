/**
 * Перебивает настройки TinyMce
 * Подходит только для StatusAdminType, т.к. явно задан селектор
 */
tinymce.init({
    selector:'textarea#status_admin_reason',
    height: 250,
    language_url : '/js/vendor/tinymce/langs/ru.js',
    branding: false,
    plugins: [
        "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "save table contextmenu directionality emoticons template paste textcolor responsivefilemanager"
    ],
    toolbar1: 'undo redo | styleselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
    toolbar2: 'link image video responsivefilemanager',
    image_advtab: true ,
    external_filemanager_path:"/fm/",
    filemanager_title: "Файловый менеджер" ,
    filemanager_access_key: "83a2de51cdff498a8c68c04a44d49045",
    external_plugins: { "filemanager" : "/fm/plugin.min.js"},
    templates: "/api/admin/template/status/cancel"
});

