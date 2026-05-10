<?php
namespace Thunderpc\Vkurse\Utils;

class Log{
    private static $file;
    public static function init(){
        self::$file = defined('LOG_FILE') ? LOG_FILE : __DIR__ . '/../../../logs/log.log';
    }

    public static function debug($msg){
        file_put_contents(self::$file, date('Y-m-d H:i:s') . ' [DEBUG] ' . $msg . PHP_EOL, FILE_APPEND);
    }

    public static function info($msg){
        file_put_contents(self::$file, date('Y-m-d H:i:s') . ' [INFO] ' . $msg . PHP_EOL, FILE_APPEND);
    }
    public static function warn($msg){
        file_put_contents(self::$file, date('Y-m-d H:i:s') . ' [WARNING] ' . $msg . PHP_EOL, FILE_APPEND);
    }
    public static function warning($msg){
        file_put_contents(self::$file, date('Y-m-d H:i:s') . ' [WARNING] ' . $msg . PHP_EOL, FILE_APPEND);
    }
    public static function error($msg){
        file_put_contents(self::$file, date('Y-m-d H:i:s') . ' [ERROR] ' . $msg . PHP_EOL, FILE_APPEND);
    }
}
?>