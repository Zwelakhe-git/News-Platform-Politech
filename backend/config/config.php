<?php
define('IMAGETYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'image/svg+xml']);
define('VIDEOTYPES', ['video/mp4', 'video/*']);
define('DOCUMENT_TYPES', ['application/pdf']);
define('ADMIN_EMAIL','zwelakhe.mzwet@gmail.com');
define('SITE_EMAIL', '');
define('SERVICE_MANAGER_EMAIL', '');
define('GOOGLE_EMAIL_PASSWORD', 'hxnz vequ oyeh qspr');
define('ADMIN_NAME', '');
define('SITE_ROOT', realpath(__DIR__ . '/..'));
define('BASE_DIR', realpath(__DIR__ . '/../..'));
define('FRONTEND_DIR', realpath(__DIR__ . '/../../frontend'));
define('HTDOCS', realpath(__DIR__ . '/../../..'));
define('ADMIN_DIR', FRONTEND_DIR . '/src/Pages/Admin');
define('TEMPLATES_DIR', FRONTEND_DIR . '/src/Pages');
define('MEDIA_ROOT', BASE_DIR . '/media');
define('UPLOAD_DIR', BASE_DIR . '/uploads');
define('IMAGES_PATH', UPLOAD_DIR . '/images');
define('VIDEOS_PATH', UPLOAD_DIR . '/videos');
define('DOCUMENTS_PATH', UPLOAD_DIR . '/documents');
define('OS_ORG_API_KEY', '');
define('OS_APP_API_KEY', '');
define('OS_PN_APP_ID', '');
define('BASE_URL', '/vkurse');
define('LOG_PATH', realpath(__DIR__ . '/../logs/log.log'));
define('LOG_FILE', realpath(__DIR__ . '/../logs/log.log'));
define("TINY_API", "g23kch440bemtvvaejf63nukznpuwd12l7nk7whkpijtejvc");
define('IMGBB_BASE_URL', 'https://api.imgbb.com/1/upload/');

function getErrorType($code) {
    $errorNames = [
        1 => 'FATAL',      // E_ERROR
        2 => 'WARNING',    // E_WARNING
        4 => 'PARSE',      // E_PARSE
        8 => 'NOTICE',     // E_NOTICE
        16 => 'CORE FATAL',
        32 => 'CORE WARNING',
        64 => 'COMPILE FATAL',
        128 => 'COMPILE WARNING',
        256 => 'USER FATAL',
        512 => 'USER WARNING',
        1024 => 'USER NOTICE',
        2048 => 'STRICT',
        4096 => 'RECOVERABLE',
        8192 => 'DEPRECATED',
        16384 => 'USER DEPRECATED'
    ];
    
    return $errorNames[$code] ?? 'UNKNOWN';
}

function log_($msg){
    file_put_contents(date('Y-m-d H:i:s') . $msg . PHP_EOL, FILE_APPEND);
}
?>