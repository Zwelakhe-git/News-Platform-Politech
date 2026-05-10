<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/backend/logs/log.log');
// В самом начале index.php или bootstrap файла
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Установите 1 если используете HTTPS
ini_set('session.cookie_samesite', 'Lax');

// Для отладки - покажите настройки сессии
$debugFile = __DIR__ . '/backend/logs/session_debug.log';
//file_put_contents($debugFile, date('Y-m-d H:i:s') . ' - Session save path: ' . session_save_path() . PHP_EOL, FILE_APPEND);
//file_put_contents($debugFile, date('Y-m-d H:i:s') . ' - Session cookie params: ' . print_r(session_get_cookie_params(), true) . PHP_EOL, FILE_APPEND);

require_once __DIR__ . '/backend/config/config.php';
require HTDOCS . '/vendor/autoload.php';
require_once __DIR__ . '/backend/controllers.php';
require_once __DIR__ . '/backend/urls.php';

use XPRSS\Application;
use XPRSS\Router;
// use zvelakeexprss\Application;
// use zvelakeexprss\Router;

$app = new Application();
$router = new Router();

function handleError($errno, $errstr, $errfile, $errline){
    $errName = getErrorType($errno);
    error_log("$errName [$errno] $errstr at $errfile line $errline");
}

function handleException($exception){
    
    error_log("Exception | {$exception->getMessage()}");
}

set_error_handler('handleError');
set_exception_handler('handleException');

foreach($urlpatterns as $pattern){
    $router->{$pattern['method']}($pattern['url'], $pattern['handler']);
}

$app->listen($router);
?>
